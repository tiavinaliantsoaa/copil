<?php

namespace App\Services;

use App\Models\CommunicationReport;

class ReportCalculator
{
    public function build(CommunicationReport $current, ?CommunicationReport $previous): array
    {
        $leads = $this->leadsByChannel($current);
        $previousLeads = $previous ? $this->leadsByChannel($previous) : [];
        $trainingAudience = $this->trainingTotal($current, 'value');
        $trainingLeads = $this->trainingTotal($current, 'leads');
        $previousTrainingAudience = $previous ? $this->trainingTotal($previous, 'value') : 0;
        $previousTrainingLeads = $previous ? $this->trainingTotal($previous, 'leads') : 0;

        return [
            'leads' => $leads,
            'total_leads' => array_sum($leads),
            'total_leads_delta' => $this->delta(array_sum($leads), array_sum($previousLeads)),
            'reach' => $this->totalGroup($current, 'reach'),
            'reach_delta' => $this->delta($this->totalGroup($current, 'reach'), $previous ? $this->totalGroup($previous, 'reach') : 0),
            'budget_rate' => $this->rate((float) ($current->summary['budget_spent'] ?? 0), (float) ($current->summary['budget_allocated'] ?? 0)),
            'completion' => $this->completion($current),
            'training_audience' => $trainingAudience,
            'training_leads' => $trainingLeads,
            'training_conversion' => $this->rate($trainingLeads, $trainingAudience),
            'training_audience_delta' => $this->delta($trainingAudience, $previousTrainingAudience),
            'training_leads_delta' => $this->delta($trainingLeads, $previousTrainingLeads),
            'previous' => $previous,
        ];
    }

    public function leadsByChannel(CommunicationReport $report): array
    {
        return collect($report->channels)->mapWithKeys(function (array $channel) {
            $lead = collect($channel['metrics'] ?? [])->firstWhere('group', 'leads');
            return [$channel['id'] => (float) ($lead['value'] ?? 0)];
        })->all();
    }

    public function totalGroup(CommunicationReport $report, string $group): float
    {
        return (float) collect($report->channels)->sum(fn (array $channel) =>
            collect($channel['metrics'] ?? [])->where('group', $group)->sum(fn ($metric) => (float) ($metric['value'] ?? 0))
        );
    }

    public function metricValue(?CommunicationReport $report, string $channelId, string $metricId): float
    {
        if (!$report) return 0;
        $channel = collect($report->channels)->firstWhere('id', $channelId);
        $metric = collect($channel['metrics'] ?? [])->firstWhere('id', $metricId);
        return (float) ($metric['value'] ?? 0);
    }

    public function delta(float $current, float $previous): array
    {
        return [
            'value' => $current - $previous,
            'percent' => $previous == 0 ? ($current == 0 ? 0 : 100) : (($current - $previous) / $previous) * 100,
        ];
    }

    private function trainingTotal(CommunicationReport $report, string $field): float
    {
        return (float) collect($report->training['channels'] ?? [])->sum(fn ($channel) => (float) ($channel[$field] ?? 0));
    }

    private function rate(float $part, float $total): float
    {
        return $total > 0 ? ($part / $total) * 100 : 0;
    }

    private function completion(CommunicationReport $report): int
    {
        $values = [
            $report->summary['progress'] ?? '', $report->summary['analysis'] ?? '',
            $report->summary['copil_decision'] ?? '', $report->summary['final_objective'] ?? '',
        ];
        foreach ($report->channels as $channel) {
            array_push($values, $channel['performance'] ?? '', $channel['analysis'] ?? '', $channel['next_action'] ?? '');
        }
        foreach ($report->action_plan as $action) $values[] = $action['action'] ?? '';
        $filled = collect($values)->filter(fn ($value) => trim((string) $value) !== '')->count();
        return (int) round(($filled / max(count($values), 1)) * 100);
    }
}
