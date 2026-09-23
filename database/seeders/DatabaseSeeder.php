<?php

namespace Database\Seeders;

use App\Models\CommunicationReport;
use App\Models\Period;
use App\Models\User;
use App\Services\ReportBlueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            ['name' => 'Admin ESCM', 'email' => 'admin@escm.mg', 'role' => 'admin'],
            ['name' => 'Direction Générale', 'email' => 'direction@escm.mg', 'role' => 'direction'],
            ['name' => 'Responsable Communication', 'email' => 'communication@escm.mg', 'role' => 'communication_owner'],
            ['name' => 'Invité Lecture seule', 'email' => 'lecture@escm.mg', 'role' => 'viewer'],
        ])->mapWithKeys(function (array $item) {
            $user = User::query()->where('email', $item['email'])->first();
            if (!$user) {
                $password = Str::password(20);
                $user = User::create(array_merge($item, ['active' => true, 'password' => Hash::make($password)]));
                $this->command?->line("Compte {$item['email']} · mot de passe temporaire: $password");
            }
            return [$user->role => $user];
        });

        foreach ([
            ['key' => '2026-07', 'label' => 'Juillet 2026', 'starts_on' => '2026-07-01', 'status' => 'closed', 'closed_at' => '2026-07-31 15:00:00'],
            ['key' => '2026-08', 'label' => 'Août 2026', 'starts_on' => '2026-08-01', 'status' => 'closed', 'closed_at' => '2026-08-31 15:00:00'],
            ['key' => '2026-09', 'label' => 'Septembre 2026', 'starts_on' => '2026-09-01', 'status' => 'open', 'closed_at' => null],
        ] as $definition) {
            $period = Period::firstOrCreate(['key' => $definition['key']], array_merge($definition, [
                'closed_by' => $definition['status'] === 'closed' ? $users['admin']->id : null,
            ]));
            CommunicationReport::firstOrCreate(['period_id' => $period->id], array_merge($this->reportData($period), [
                'updated_by' => $users['communication_owner']->id,
            ]));
        }
    }

    private function reportData(Period $period): array
    {
        $month = (int) $period->starts_on->format('m');
        $isSeptember = $month === 9;
        $values = match ($month) {
            9 => [
                'facebook' => [31800, 21400, 486, 142], 'instagram' => [22400, 1850, 936, 128, 96],
                'tiktok' => [41800, 1480, 3920, 74], 'linkedin' => [12600, 468, 86, 592, 31],
                'website' => [2940, 78500, 3.75, 12, 58], 'email' => [3510, 43.6, 9.2, 19],
                'sms' => [1310, 96.8, 12.4, 8],
            ],
            8 => [
                'facebook' => [28400, 18900, 425, 121], 'instagram' => [19600, 1590, 802, 111, 82],
                'tiktok' => [36500, 1290, 3410, 61], 'linkedin' => [11100, 397, 72, 501, 29],
                'website' => [2670, 71900, 3.71, 10, 55], 'email' => [3260, 41.4, 8.1, 20],
                'sms' => [1250, 96.1, 10.8, 8],
            ],
            default => [
                'facebook' => [25100, 17200, 382, 102], 'instagram' => [17100, 1380, 704, 96, 73],
                'tiktok' => [33200, 1130, 2950, 58], 'linkedin' => [9800, 351, 64, 446, 24],
                'website' => [2380, 65400, 3.64, 9, 48], 'email' => [3010, 40.1, 7.4, 18],
                'sms' => [1180, 95.7, 10.1, 8],
            ],
        };

        $base = ReportBlueprint::report($period);
        $base['status'] = $isSeptember ? 'En cours' : 'Terminé';
        $base['due_date'] = $isSeptember ? '2026-10-03' : $period->starts_on->copy()->endOfMonth()->format('Y-m-d');
        $base['summary'] = [
            'progress' => $isSeptember ? 'Les leads progressent sur cinq canaux, avec une contribution forte de Facebook et Instagram.' : 'La portée digitale et les demandes entrantes progressent.',
            'decline' => $isSeptember ? 'La conversion SMS reste inférieure au niveau attendu.' : 'La conversion TikTok reste à consolider.',
            'analysis' => 'Les contenus incarnés et les campagnes segmentées génèrent les meilleurs résultats.',
            'weak_signal' => 'La hausse de portée ne se traduit pas encore uniformément en rendez-vous qualifiés.',
            'maintain' => 'Les témoignages étudiants, les Reels courts et les relances ciblées.',
            'test' => 'Des liens suivis par plateforme et un formulaire simplifié sur mobile.',
            'copil_decision' => 'Arbitrer le budget d’octobre selon le coût par lead et la qualité des rendez-vous.',
            'priority_channel' => 'facebook',
            'final_objective' => 'Atteindre 500 leads qualifiés et améliorer leur traçabilité jusqu’au rendez-vous.',
            'budget_allocated' => $month === 9 ? 10500000 : ($month === 8 ? 9200000 : 8700000),
            'budget_spent' => $month === 9 ? 8650000 : ($month === 8 ? 7820000 : 7480000),
            'budget_note' => 'Le report d’une activation terrain explique la sous-consommation du mois.',
            'blockers' => ['Validation tardive de deux visuels institutionnels.', 'Traçabilité incomplète entre certains formulaires et les outils de suivi.'],
            'comments' => 'Le mois confirme la progression digitale. La priorité porte désormais sur la qualité et le suivi des leads.',
        ];
        $base['channels'] = $this->channels($base['channels'], $values);
        $base['graphics'] = [
            'visuals_produced' => $month === 9 ? 64 : ($month === 8 ? 57 : 51),
            'campaigns_created' => $month === 9 ? 6 : 5,
            'average_delivery_days' => $month === 9 ? 2.4 : 2.8,
            'major_revisions' => $month === 9 ? 3 : 4,
            'note' => 'Les nouveaux gabarits Admissions réduisent les corrections et harmonisent les déclinaisons.',
        ];
        $base['training'] = $this->training($month);
        $base['tools'] = $this->tools($month);
        $base['events'] = $this->events($period->key);
        $base['projects'] = $this->projects($month);
        $base['action_plan'] = $this->actions($period->key, $isSeptember);

        return $base;
    }

    private function channels(array $channels, array $values): array
    {
        $copy = [
            'facebook' => ['Les clics et les contacts progressent avec la campagne rentrée.', 'Les témoignages étudiants et les formats vidéo génèrent le plus de conversations.', 'Renforcer les campagnes à formulaire et recibler les visiteurs engagés.', 'Maintenir le budget Meta sur les audiences qui génèrent des rendez-vous.', 'Reel journée d’intégration', 'Visages identifiables, rythme court et preuve concrète de la vie étudiante.', 'Visuel frais de scolarité', 'Message trop dense et appel à l’action peu visible.'],
            'instagram' => ['Les Reels portent la croissance des vues et des visites de profil.', 'Les contenus incarnés dépassent les visuels institutionnels.', 'Publier deux Reels étudiants par semaine.', 'Valider un rythme vidéo régulier pendant la campagne admissions.', 'Une journée avec une étudiante ESCM', 'Format immersif, montage rapide et commentaires naturels.', 'Carrousel programme académique', 'Première page trop abstraite et bénéfice peu immédiat.'],
            'tiktok' => ['La portée augmente, mais le passage vers le formulaire reste limité.', 'Les vidéos avec un hook immédiat retiennent mieux.', 'Tester un appel à l’action oral et un lien dédié.', 'Poursuivre TikTok avec un suivi précis des leads.', 'POV premier jour à ESCM', 'Hook immédiat et présence étudiante forte.', 'Présentation institutionnelle du campus', 'Introduction lente et ton trop formel.'],
            'linkedin' => ['Les publications partenaires soutiennent les impressions et les demandes B2B.', 'Les résultats chiffrés renforcent la crédibilité.', 'Publier un cas partenaire et identifier les intervenants.', 'Prioriser les preuves d’impact.', 'Partenariat entreprise et insertion', 'Résultat concret et partenaire identifié.', 'Annonce générique de rentrée', 'Peu de preuve et contenu trop générique.'],
            'website' => ['Admissions et Programmes concentrent les clics organiques.', 'Les requêtes orientées métier convertissent mieux.', 'Optimiser la page Admissions et publier un article métier.', 'Prioriser les pages qui contribuent aux formulaires.', 'Page Admissions 2026', 'Informations pratiques et formulaire accessibles.', 'Page programme Management', 'Titre SEO générique et CTA trop bas.'],
            'email' => ['Les campagnes segmentées progressent en ouverture et en conversion.', 'Les objets courts et centrés sur une action obtiennent les meilleurs clics.', 'Automatiser la séquence des dossiers incomplets.', 'Conserver la segmentation par niveau d’intention.', 'Email dossiers incomplets', 'Objet précis, message direct et échéance visible.', 'Newsletter générale de rentrée', 'Audience trop large et plusieurs CTA.'],
            'sms' => ['Les rappels courts déclenchent des réponses rapides.', 'Un message nominatif avec une consigne réduit les abandons.', 'Planifier deux relances sur les dossiers prioritaires.', 'Limiter les envois aux prospects consentants.', 'Rappel clôture des admissions', 'Message court, date explicite et lien unique.', 'SMS général portes ouvertes', 'Ciblage trop large et bénéfice peu personnalisé.'],
        ];

        return collect($channels)->map(function (array $channel) use ($values, $copy) {
            foreach ($channel['metrics'] as $index => &$metric) $metric['value'] = $values[$channel['id']][$index] ?? 0;
            [$channel['performance'], $channel['analysis'], $channel['next_action'], $channel['decision'], $best, $bestReason, $improve, $improveReason] = $copy[$channel['id']];
            $channel['content'] = [
                'best_title' => $best, 'best_reason' => $bestReason,
                'improvement_title' => $improve, 'improvement_reason' => $improveReason,
                'keep' => 'Conserver le format et le ciblage du meilleur contenu.',
                'test' => 'Tester une accroche plus directe et un appel à l’action unique.',
            ];
            return $channel;
        })->all();
    }

    private function training(int $month): array
    {
        $september = $month === 9;
        return [
            'channels' => [
                ['id' => 'facebook-fp', 'label' => 'Facebook FP', 'objective' => 'Visibilité et demandes', 'value' => $september ? 18400 : 16100, 'leads' => $september ? 38 : 31, 'action' => 'Renforcer les témoignages participants.'],
                ['id' => 'linkedin-fp', 'label' => 'LinkedIn FP', 'objective' => 'Crédibilité B2B', 'value' => $september ? 7900 : 6800, 'leads' => $september ? 17 : 14, 'action' => 'Publier un cas entreprise.'],
                ['id' => 'website-fp', 'label' => 'Site / landing FP', 'objective' => 'Trafic et conversion', 'value' => $september ? 1180 : 990, 'leads' => $september ? 22 : 18, 'action' => 'Simplifier le formulaire entreprise.'],
                ['id' => 'email-fp', 'label' => 'Email FP', 'objective' => 'Nurturing entreprises', 'value' => $september ? 620 : 530, 'leads' => $september ? 9 : 7, 'action' => 'Segmenter par secteur d’activité.'],
                ['id' => 'sms-fp', 'label' => 'SMS FP', 'objective' => 'Rappels directs', 'value' => $september ? 240 : 210, 'leads' => $september ? 5 : 4, 'action' => 'Cibler les prospects engagés.'],
            ],
            'impressions' => $september ? 26300 : 22900, 'interactions' => $september ? 1140 : 930,
            'clicks' => $september ? 624 : 515, 'inbound_requests' => $september ? 91 : 74,
            'format_to_keep' => 'Témoignage entreprise avec résultat concret.',
            'strong_message' => 'Former les équipes sur des besoins directement applicables.',
            'offer_to_promote' => 'Management opérationnel pour responsables d’équipe.',
            'priority_audience' => 'DRH et dirigeants de PME à Antananarivo.',
            'cta_to_test' => 'Demander le programme entreprise.',
            'testimonial' => 'Retour d’expérience d’un manager ayant suivi la formation.',
            'featured_training' => 'Management opérationnel', 'priority_channel' => 'LinkedIn FP',
            'decision' => 'Valider un budget de génération de leads B2B pour octobre.',
        ];
    }

    private function tools(int $month): array
    {
        $plus = $month === 9 ? 10 : 0;
        return [
            ['id' => 'outil-communication', 'name' => 'Outil Communication', 'status' => 'En cours', 'progress' => 62 + $plus, 'blocker' => 'Import des anciens projets à finaliser.', 'decision' => 'Valider la structure des droits utilisateurs.', 'next_milestone' => 'Module calendrier éditorial le 8 octobre.'],
            ['id' => 'pipeline-admissions', 'name' => 'Pipeline Admissions V2', 'status' => 'En cours', 'progress' => 54 + $plus, 'blocker' => 'Correspondance des sources de leads.', 'decision' => 'Valider la nomenclature des campagnes.', 'next_milestone' => 'Test du parcours Admissions.'],
            ['id' => 'erp-beta', 'name' => 'ERP Beta', 'status' => 'En cours', 'progress' => 38 + $plus, 'blocker' => 'Synchronisation des inscriptions.', 'decision' => 'Prioriser les données utiles au COPIL.', 'next_milestone' => 'Recette fonctionnelle mi-octobre.'],
            ['id' => 'agent-ia', 'name' => 'Agent IA', 'status' => 'À faire', 'progress' => 15 + $plus, 'blocker' => 'Cadre d’utilisation à formaliser.', 'decision' => 'Choisir deux cas d’usage pilotes.', 'next_milestone' => 'Test de synthèse des commentaires.'],
        ];
    }

    private function events(string $period): array
    {
        return [
            ['id' => "integration-$period", 'name' => 'Journées d’intégration', 'objective' => 'Valoriser l’expérience étudiante', 'photos' => 186, 'reels' => 7, 'results' => '128 000 vues cumulées et 46 demandes attribuées.', 'next_step' => 'Décliner les témoignages en campagne Admissions.'],
            ['id' => "orientation-$period", 'name' => 'Salon de l’orientation', 'objective' => 'Collecter des contacts qualifiés', 'photos' => 94, 'reels' => 4, 'results' => '117 contacts collectés, dont 63 qualifiés.', 'next_step' => 'Relancer les prospects sous 72 heures.'],
        ];
    }

    private function projects(int $month): array
    {
        $plus = $month === 9 ? 12 : 0;
        return [
            ['id' => 'sous-sol', 'name' => 'Sous-sol', 'owner' => 'Direction / Communication', 'status' => 'En cours', 'risk' => 'Arbitrage signalétique et budget.', 'next_action' => 'Valider le plan d’aménagement visuel.', 'progress' => 33 + $plus],
            ['id' => 'campagne-admissions', 'name' => 'Campagne admissions', 'owner' => 'Responsable Communication', 'status' => 'En cours', 'risk' => 'Coût par lead variable selon le canal.', 'next_action' => 'Réallouer le budget selon la qualité des leads.', 'progress' => 66 + $plus],
            ['id' => 'production-contenus', 'name' => 'Production contenus', 'owner' => 'Équipe Communication', 'status' => 'En cours', 'risk' => 'Capacité vidéo limitée sur les semaines événementielles.', 'next_action' => 'Bloquer deux journées de tournage récurrentes.', 'progress' => 57 + $plus],
        ];
    }

    private function actions(string $period, bool $september): array
    {
        return [
            ['id' => "cout-lead-$period", 'priority' => 'Haute', 'action' => 'Finaliser le coût par lead par canal.', 'owner' => 'Responsable Communication', 'due_date' => '2026-10-06', 'expected_kpi' => '100 % des leads attribués', 'status' => 'En cours'],
            ['id' => "admissions-$period", 'priority' => 'Haute', 'action' => 'Lancer la campagne Admissions d’octobre.', 'owner' => 'Équipe digitale', 'due_date' => '2026-10-08', 'expected_kpi' => '500 leads qualifiés', 'status' => 'À faire'],
            ['id' => "formation-$period", 'priority' => 'Moyenne', 'action' => 'Publier le cas partenaire Formation Pro.', 'owner' => 'Content manager', 'due_date' => '2026-10-11', 'expected_kpi' => '15 demandes B2B', 'status' => $september ? 'À faire' : 'Terminé'],
        ];
    }
}
