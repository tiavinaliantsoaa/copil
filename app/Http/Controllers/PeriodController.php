<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CommunicationReport;
use App\Models\Period;
use App\Services\ReportBlueprint;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['month' => ['required', 'date_format:Y-m', 'unique:periods,key']]);
        $date = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        $period = DB::transaction(function () use ($date, $request) {
            $period = Period::create([
                'key' => $date->format('Y-m'),
                'label' => ucfirst($date->copy()->locale('fr')->translatedFormat('F Y')),
                'starts_on' => $date,
            ]);
            $previous = Period::query()->where('starts_on', '<', $date)->orderByDesc('starts_on')->first()?->report;
            $period->report()->create(array_merge(ReportBlueprint::report($period, $previous), ['updated_by' => $request->user()->id]));
            return $period;
        });

        $this->audit($request, $period, 'period.created');

        return redirect()->route('copil.index', ['period' => $period->key])->with('success', 'Le nouveau mois est prêt à être renseigné.');
    }

    public function close(Request $request, Period $period): RedirectResponse
    {
        $period->update(['status' => 'closed', 'closed_at' => now(), 'closed_by' => $request->user()->id]);
        $period->report()->update(['status' => 'Terminé', 'updated_by' => $request->user()->id]);
        $this->audit($request, $period, 'period.closed');

        return back()->with('success', 'Le COPIL est clôturé et verrouillé.');
    }

    public function reopen(Request $request, Period $period): RedirectResponse
    {
        $period->update(['status' => 'open', 'closed_at' => null, 'closed_by' => null]);
        $this->audit($request, $period, 'period.reopened');

        return back()->with('success', 'Le COPIL est de nouveau modifiable.');
    }

    private function audit(Request $request, Period $period, string $action): void
    {
        AuditLog::create([
            'user_id' => $request->user()->id, 'action' => $action,
            'subject_type' => Period::class, 'subject_id' => (string) $period->id,
            'context' => ['period' => $period->key], 'ip_address' => $request->ip(),
        ]);
    }
}
