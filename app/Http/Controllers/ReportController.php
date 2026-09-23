<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CommunicationReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function updateSection(Request $request, CommunicationReport $report, string $section): RedirectResponse
    {
        $this->authorizeEdit($request, $report);
        abort_unless(in_array($section, ['meta', 'summary', 'channels', 'graphics', 'training', 'tools', 'events', 'projects', 'actions'], true), 404);

        $validated = $request->validate($this->rules($section));

        if ($section === 'meta') {
            $report->fill($validated['meta']);
        } else {
            $input = $section === 'actions' ? 'action_plan' : $section;
            $value = $validated[$input] ?? [];
            if (in_array($section, ['tools', 'events', 'projects', 'actions'], true)) {
                $value = array_values($value);
            }
            if ($section === 'training' && isset($value['channels'])) {
                $value['channels'] = array_values($value['channels']);
            }
            $report->{$input} = $value;
        }
        $report->updated_by = $request->user()->id;
        $report->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'report.section.updated',
            'subject_type' => CommunicationReport::class,
            'subject_id' => (string) $report->id,
            'context' => ['section' => $section, 'period' => $report->period->key],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('copil.index', ['period' => $report->period->key])
            ->withFragment($request->string('_panel', $section)->toString())
            ->with('success', 'Les données ont été enregistrées.');
    }

    private function authorizeEdit(Request $request, CommunicationReport $report): void
    {
        abort_unless($request->user()->canEditReports(), 403);
        abort_if($report->period->isClosed() && !$request->user()->isAdmin(), 423, 'Cette période est clôturée.');
    }

    private function rules(string $section): array
    {
        $text = ['nullable', 'string', 'max:5000'];

        return match ($section) {
            'meta' => [
                'meta.owner' => ['required', 'string', 'max:150'],
                'meta.due_date' => ['nullable', 'date'],
                'meta.status' => ['required', Rule::in(['À faire', 'En cours', 'Bloqué', 'Terminé'])],
            ],
            'summary' => [
                'summary' => ['required', 'array'],
                'summary.progress' => $text, 'summary.decline' => $text, 'summary.analysis' => $text,
                'summary.weak_signal' => $text, 'summary.maintain' => $text, 'summary.test' => $text,
                'summary.copil_decision' => $text, 'summary.final_objective' => $text,
                'summary.priority_channel' => ['required', Rule::in(['facebook', 'instagram', 'tiktok', 'linkedin', 'website', 'email', 'sms'])],
                'summary.budget_allocated' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
                'summary.budget_spent' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
                'summary.budget_note' => $text, 'summary.comments' => $text,
                'summary.blockers' => ['nullable', 'array', 'max:20'],
                'summary.blockers.*' => $text,
            ],
            'channels' => [
                'channels' => ['required', 'array', 'size:7'],
                'channels.*.id' => ['required', 'string', 'max:40'],
                'channels.*.label' => ['required', 'string', 'max:80'],
                'channels.*.objective' => ['nullable', 'string', 'max:500'],
                'channels.*.metrics' => ['required', 'array', 'max:10'],
                'channels.*.metrics.*.id' => ['required', 'string', 'max:60'],
                'channels.*.metrics.*.label' => ['required', 'string', 'max:100'],
                'channels.*.metrics.*.value' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
                'channels.*.metrics.*.group' => ['required', 'string', 'max:30'],
                'channels.*.metrics.*.unit' => ['required', Rule::in(['number', 'percent'])],
                'channels.*.performance' => $text, 'channels.*.analysis' => $text,
                'channels.*.next_action' => $text, 'channels.*.decision' => $text,
                'channels.*.content' => ['required', 'array'],
                'channels.*.content.best_title' => $text, 'channels.*.content.best_reason' => $text,
                'channels.*.content.improvement_title' => $text, 'channels.*.content.improvement_reason' => $text,
                'channels.*.content.keep' => $text, 'channels.*.content.test' => $text,
            ],
            'graphics' => [
                'graphics' => ['required', 'array'],
                'graphics.visuals_produced' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'graphics.campaigns_created' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'graphics.average_delivery_days' => ['nullable', 'numeric', 'min:0', 'max:365'],
                'graphics.major_revisions' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'graphics.note' => $text,
            ],
            'training' => [
                'training' => ['required', 'array'],
                'training.channels' => ['required', 'array', 'size:5'],
                'training.channels.*.id' => ['required', 'string', 'max:40'],
                'training.channels.*.label' => ['required', 'string', 'max:80'],
                'training.channels.*.objective' => ['nullable', 'string', 'max:500'],
                'training.channels.*.value' => ['nullable', 'numeric', 'min:0'],
                'training.channels.*.leads' => ['nullable', 'numeric', 'min:0'],
                'training.channels.*.action' => $text,
                'training.impressions' => ['nullable', 'numeric', 'min:0'],
                'training.interactions' => ['nullable', 'numeric', 'min:0'],
                'training.clicks' => ['nullable', 'numeric', 'min:0'],
                'training.inbound_requests' => ['nullable', 'numeric', 'min:0'],
                'training.format_to_keep' => $text, 'training.strong_message' => $text,
                'training.offer_to_promote' => $text, 'training.priority_audience' => $text,
                'training.cta_to_test' => $text, 'training.testimonial' => $text,
                'training.featured_training' => $text, 'training.priority_channel' => $text,
                'training.decision' => $text,
            ],
            'tools' => $this->listRules('tools', ['name', 'status', 'progress', 'blocker', 'decision', 'next_milestone']),
            'events' => $this->listRules('events', ['name', 'objective', 'photos', 'reels', 'results', 'next_step']),
            'projects' => $this->listRules('projects', ['name', 'owner', 'status', 'risk', 'next_action', 'progress']),
            'actions' => $this->listRules('action_plan', ['priority', 'action', 'owner', 'due_date', 'expected_kpi', 'status']),
        };
    }

    private function listRules(string $root, array $fields): array
    {
        $rules = [$root => ['nullable', 'array', 'max:100'], "$root.*.id" => ['required', 'string', 'max:80']];
        foreach ($fields as $field) {
            $rules["$root.*.$field"] = match ($field) {
                'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
                'photos', 'reels' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'due_date' => ['nullable', 'date'],
                'status' => ['nullable', Rule::in(['À faire', 'En cours', 'Bloqué', 'Terminé'])],
                'priority' => ['nullable', Rule::in(['Haute', 'Moyenne', 'Basse'])],
                default => ['nullable', 'string', 'max:5000'],
            };
        }
        return $rules;
    }
}
