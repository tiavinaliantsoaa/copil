<?php

namespace App\Services;

use App\Models\CommunicationReport;
use App\Models\Period;

class ReportBlueprint
{
    public static function channels(): array
    {
        return [
            self::channel('facebook', 'Facebook', 'Contacts, trafic et notoriété', [
                ['id' => 'views', 'label' => 'Vues', 'value' => 0, 'group' => 'reach', 'unit' => 'number'],
                ['id' => 'spectators', 'label' => 'Spectateurs', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'clicks', 'label' => 'Clics lien', 'value' => 0, 'group' => 'traffic', 'unit' => 'number'],
                ['id' => 'leads', 'label' => 'Leads / contacts', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('instagram', 'Instagram', 'Engagement et image de marque', [
                ['id' => 'views', 'label' => 'Vues', 'value' => 0, 'group' => 'reach', 'unit' => 'number'],
                ['id' => 'interactions', 'label' => 'Interactions', 'value' => 0, 'group' => 'engagement', 'unit' => 'number'],
                ['id' => 'profile-visits', 'label' => 'Visites profil', 'value' => 0, 'group' => 'traffic', 'unit' => 'number'],
                ['id' => 'followers', 'label' => 'Nouveaux abonnés', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'leads', 'label' => 'Leads / messages', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('tiktok', 'TikTok', 'Portée, préférence et prospects', [
                ['id' => 'views', 'label' => 'Vues publications', 'value' => 0, 'group' => 'reach', 'unit' => 'number'],
                ['id' => 'profile-views', 'label' => 'Vues profil', 'value' => 0, 'group' => 'traffic', 'unit' => 'number'],
                ['id' => 'likes', 'label' => 'J’aime', 'value' => 0, 'group' => 'engagement', 'unit' => 'number'],
                ['id' => 'leads', 'label' => 'Partages', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('linkedin', 'LinkedIn', 'Crédibilité, partenaires et B2B', [
                ['id' => 'impressions', 'label' => 'Impressions', 'value' => 0, 'group' => 'reach', 'unit' => 'number'],
                ['id' => 'reactions', 'label' => 'Réactions', 'value' => 0, 'group' => 'engagement', 'unit' => 'number'],
                ['id' => 'followers', 'label' => 'Nouveaux abonnés', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'visitors', 'label' => 'Visiteurs', 'value' => 0, 'group' => 'traffic', 'unit' => 'number'],
                ['id' => 'leads', 'label' => 'Leads B2B', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('website', 'Site & Google', 'Trafic organique et formulaires', [
                ['id' => 'clicks', 'label' => 'Clics', 'value' => 0, 'group' => 'traffic', 'unit' => 'number'],
                ['id' => 'impressions', 'label' => 'Impressions', 'value' => 0, 'group' => 'reach', 'unit' => 'number'],
                ['id' => 'ctr', 'label' => 'CTR', 'value' => 0, 'group' => 'rate', 'unit' => 'percent'],
                ['id' => 'key-pages', 'label' => 'Pages clés', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'leads', 'label' => 'Leads formulaires', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('email', 'Email', 'Nurturing et conversion', [
                ['id' => 'contacts', 'label' => 'Base contacts', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'open-rate', 'label' => 'Taux d’ouverture', 'value' => 0, 'group' => 'rate', 'unit' => 'percent'],
                ['id' => 'click-rate', 'label' => 'Taux de clic', 'value' => 0, 'group' => 'rate', 'unit' => 'percent'],
                ['id' => 'leads', 'label' => 'Désinscrits', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
            self::channel('sms', 'SMS', 'Relance directe et réponse', [
                ['id' => 'contacts', 'label' => 'Contacts ciblés', 'value' => 0, 'group' => 'audience', 'unit' => 'number'],
                ['id' => 'delivery-rate', 'label' => 'Taux de délivrabilité', 'value' => 0, 'group' => 'rate', 'unit' => 'percent'],
                ['id' => 'response-rate', 'label' => 'Taux de réponse', 'value' => 0, 'group' => 'rate', 'unit' => 'percent'],
                ['id' => 'leads', 'label' => 'Conversions SMS', 'value' => 0, 'group' => 'leads', 'unit' => 'number'],
            ]),
        ];
    }

    public static function withCurrentMetricLabels(array $channels): array
    {
        $definitions = collect(self::channels())->keyBy('id');

        return array_map(function (array $channel) use ($definitions) {
            $definition = $definitions->get($channel['id'] ?? '');
            if (!$definition) {
                return $channel;
            }

            $metrics = collect($definition['metrics'])->keyBy('id');
            $channel['metrics'] = array_map(function (array $metric) use ($metrics) {
                $current = $metrics->get($metric['id'] ?? '');
                if ($current) {
                    $metric['label'] = $current['label'];
                }

                return $metric;
            }, $channel['metrics'] ?? []);

            return $channel;
        }, $channels);
    }

    private static function channel(string $id, string $label, string $objective, array $metrics): array
    {
        return [
            'id' => $id, 'label' => $label, 'objective' => $objective, 'metrics' => $metrics,
            'performance' => '', 'analysis' => '', 'next_action' => '', 'decision' => '',
            'content' => [
                'best_title' => '', 'best_reason' => '', 'improvement_title' => '',
                'improvement_reason' => '', 'keep' => '', 'test' => '',
            ],
        ];
    }

    public static function report(Period $period, ?CommunicationReport $previous = null): array
    {
        $channels = self::channels();
        if ($previous) {
            $channels = array_map(function (array $channel) use ($previous) {
                $old = collect($previous->channels)->firstWhere('id', $channel['id']);
                if (!$old) return $channel;
                $channel['objective'] = $old['objective'] ?? $channel['objective'];
                return $channel;
            }, $channels);
        }

        $trainingChannels = collect($previous?->training['channels'] ?? self::trainingChannels())
            ->map(fn (array $item) => array_merge($item, ['value' => 0, 'leads' => 0, 'action' => '']))->values()->all();

        $tools = collect($previous?->tools ?? [])->map(fn (array $item) => array_merge($item, [
            'status' => 'À faire', 'progress' => 0, 'blocker' => '', 'decision' => '', 'next_milestone' => '',
        ]))->values()->all();

        return [
            'owner' => $previous?->owner ?? 'Responsable Communication',
            'due_date' => $period->starts_on->copy()->addMonth()->day(3),
            'status' => 'À faire',
            'summary' => [
                'progress' => '', 'decline' => '', 'analysis' => '', 'weak_signal' => '',
                'maintain' => '', 'test' => '', 'copil_decision' => '',
                'priority_channel' => 'facebook', 'final_objective' => '',
                'budget_allocated' => 0, 'budget_spent' => 0, 'budget_note' => '',
                'blockers' => [''], 'comments' => '',
            ],
            'channels' => $channels,
            'graphics' => [
                'visuals_produced' => 0, 'campaigns_created' => 0,
                'average_delivery_days' => 0, 'major_revisions' => 0, 'note' => '',
            ],
            'training' => [
                'channels' => $trainingChannels, 'impressions' => 0, 'interactions' => 0,
                'clicks' => 0, 'inbound_requests' => 0, 'format_to_keep' => '',
                'strong_message' => '', 'offer_to_promote' => '', 'priority_audience' => '',
                'cta_to_test' => '', 'testimonial' => '', 'featured_training' => '',
                'priority_channel' => '', 'decision' => '',
            ],
            'tools' => $tools,
            'events' => [],
            'projects' => [],
            'action_plan' => [],
        ];
    }

    public static function trainingChannels(): array
    {
        return [
            ['id' => 'facebook-fp', 'label' => 'Facebook FP', 'objective' => 'Visibilité et demandes', 'value' => 0, 'leads' => 0, 'action' => ''],
            ['id' => 'linkedin-fp', 'label' => 'LinkedIn FP', 'objective' => 'Crédibilité B2B', 'value' => 0, 'leads' => 0, 'action' => ''],
            ['id' => 'website-fp', 'label' => 'Site / landing FP', 'objective' => 'Trafic et conversion', 'value' => 0, 'leads' => 0, 'action' => ''],
            ['id' => 'email-fp', 'label' => 'Email FP', 'objective' => 'Nurturing entreprises', 'value' => 0, 'leads' => 0, 'action' => ''],
            ['id' => 'sms-fp', 'label' => 'SMS FP', 'objective' => 'Rappels directs', 'value' => 0, 'leads' => 0, 'action' => ''],
        ];
    }
}
