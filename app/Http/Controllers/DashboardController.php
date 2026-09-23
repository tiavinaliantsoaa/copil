<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\User;
use App\Services\ReportCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ReportCalculator $calculator): View
    {
        $periods = Period::query()->with('report')->orderByDesc('starts_on')->get();

        if ($periods->isEmpty()) {
            return view('dashboard-empty', [
                'users' => $request->user()->isAdmin() ? User::query()->orderBy('name')->get() : collect(),
            ]);
        }

        $period = $periods->firstWhere('key', $request->string('period')->toString()) ?? $periods->first();
        $report = $period->report()->with('attachments')->firstOrFail();
        $previousPeriod = Period::query()->where('starts_on', '<', $period->starts_on)->orderByDesc('starts_on')->first();
        $previous = $previousPeriod?->report;
        $stats = $calculator->build($report, $previous);
        $canEdit = $request->user()->canEditReports() && (!$period->isClosed() || $request->user()->isAdmin());

        return view('dashboard', [
            'periods' => $periods,
            'period' => $period,
            'report' => $report,
            'previousPeriod' => $previousPeriod,
            'previous' => $previous,
            'stats' => $stats,
            'canEdit' => $canEdit,
            'attachments' => $report->attachments->groupBy('category'),
            'users' => $request->user()->isAdmin() ? User::query()->orderBy('name')->get() : collect(),
            'calculator' => $calculator,
        ]);
    }
}
