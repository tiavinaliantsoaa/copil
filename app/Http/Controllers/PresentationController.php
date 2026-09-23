<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Services\ReportCalculator;
use Illuminate\View\View;

class PresentationController extends Controller
{
    public function __invoke(Period $period, ReportCalculator $calculator): View
    {
        $report = $period->report()->with('attachments')->firstOrFail();
        $previous = Period::query()->where('starts_on', '<', $period->starts_on)->orderByDesc('starts_on')->first()?->report;

        return view('presentation', [
            'period' => $period,
            'report' => $report,
            'previous' => $previous,
            'stats' => $calculator->build($report, $previous),
            'attachments' => $report->attachments->groupBy('category'),
        ]);
    }
}
