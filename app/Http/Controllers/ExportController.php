<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Services\PptxExportService;
use App\Services\ReportCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function pdf(Period $period, ReportCalculator $calculator): \Illuminate\Http\Response
    {
        $report = $period->report()->with('attachments')->firstOrFail();
        $previous = Period::query()->where('starts_on', '<', $period->starts_on)->orderByDesc('starts_on')->first()?->report;
        $pdf = Pdf::loadView('exports.pdf', [
            'period' => $period,
            'report' => $report,
            'stats' => $calculator->build($report, $previous),
            'attachments' => $report->attachments->groupBy('category'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("ESCM-COPIL-Communication-{$period->key}.pdf");
    }

    public function pptx(Period $period, PptxExportService $exporter, ReportCalculator $calculator): BinaryFileResponse
    {
        $report = $period->report()->with('attachments')->firstOrFail();
        $previous = Period::query()->where('starts_on', '<', $period->starts_on)->orderByDesc('starts_on')->first()?->report;
        $path = $exporter->generate($period, $report, $calculator->build($report, $previous));

        return response()->download($path, "ESCM-COPIL-Communication-{$period->key}.pptx")->deleteFileAfterSend(true);
    }
}
