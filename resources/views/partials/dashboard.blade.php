<section class="panel" data-panel="dashboard">
    <header class="panel-head">
        <div><p class="eyebrow">Vue générale</p><h2>Synthèse du mois</h2><p>Les indicateurs et écarts sont recalculés automatiquement à partir des données enregistrées.</p></div>
        <span class="tag {{ $period->isClosed() ? 'tag-closed' : 'tag-open' }}">{{ $period->isClosed() ? 'Clôturé' : 'En cours' }}</span>
    </header>

    <div class="kpi-grid">
        @php $leadDelta = $stats['total_leads_delta']; $reachDelta = $stats['reach_delta']; @endphp
        <article class="kpi"><p class="kpi-label">Leads consolidés</p><p class="kpi-value">{{ number_format($stats['total_leads'], 0, ',', ' ') }}</p><p class="delta {{ $leadDelta['value'] < 0 ? 'negative' : '' }}">{{ $leadDelta['value'] > 0 ? '+' : '' }}{{ number_format($leadDelta['value'], 0, ',', ' ') }} · {{ $leadDelta['percent'] > 0 ? '+' : '' }}{{ number_format($leadDelta['percent'], 1, ',', ' ') }} % vs M-1</p></article>
        <article class="kpi"><p class="kpi-label">Portée totale</p><p class="kpi-value">{{ number_format($stats['reach'], 0, ',', ' ') }}</p><p class="delta {{ $reachDelta['value'] < 0 ? 'negative' : '' }}">{{ $reachDelta['value'] > 0 ? '+' : '' }}{{ number_format($reachDelta['percent'], 1, ',', ' ') }} % vs M-1</p></article>
        <article class="kpi"><p class="kpi-label">Budget consommé</p><p class="kpi-value">{{ number_format($stats['budget_rate'], 1, ',', ' ') }} %</p><p class="mt-2 text-xs font-semibold text-zinc-500">{{ number_format((float)($report->summary['budget_spent'] ?? 0), 0, ',', ' ') }} Ar dépensés</p></article>
        <article class="kpi"><p class="kpi-label">Complétude</p><p class="kpi-value">{{ $stats['completion'] }} %</p><div class="progress-track mt-3"><div class="progress-bar" style="width: {{ $stats['completion'] }}%"></div></div></article>
    </div>

    <div class="grid-2">
        <article class="surface"><div class="surface-title"><div><h3>Répartition des leads</h3><p>Part de chaque plateforme dans le total mensuel.</p></div><span class="tag tag-red">Camembert</span></div><div class="chart-box"><canvas id="leadsChart"></canvas></div></article>
        <article class="surface"><div class="surface-title"><div><h3>Comparaison M-1</h3><p>{{ $period->label }} comparé à {{ $previousPeriod?->label ?? 'la période précédente' }}.</p></div></div><div class="chart-box"><canvas id="comparisonChart"></canvas></div></article>
    </div>

    <form method="POST" action="{{ route('reports.sections.update', [$report, 'meta']) }}" class="surface">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="dashboard">
        <div class="surface-title"><div><h3>Pilotage du reporting</h3><p>Responsable, échéance et statut d’avancement.</p></div></div>
        <div class="field-grid lg:grid-cols-3">
            <label class="field"><span>Responsable</span><input class="input" name="meta[owner]" value="{{ old('meta.owner', $report->owner) }}" @disabled(!$canEdit) required></label>
            <label class="field"><span>Échéance</span><input class="input" type="date" name="meta[due_date]" value="{{ old('meta.due_date', $report->due_date?->format('Y-m-d')) }}" @disabled(!$canEdit)></label>
            <label class="field"><span>Statut</span><select class="select" name="meta[status]" @disabled(!$canEdit)>@foreach(['À faire','En cours','Bloqué','Terminé'] as $status)<option @selected($report->status === $status)>{{ $status }}</option>@endforeach</select></label>
        </div>
        @if($canEdit)<div class="mt-5 flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer</button></div>@endif
    </form>

    <form method="POST" action="{{ route('reports.sections.update', [$report, 'summary']) }}" class="space-y-5">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="dashboard">
        <div class="surface">
            <div class="surface-title"><div><h3>Lecture Direction</h3><p>Messages clés et arbitrages attendus au COPIL.</p></div></div>
            <div class="field-grid">
                @foreach([
                    'progress' => 'Ce qui progresse', 'decline' => 'Ce qui recule', 'analysis' => 'Analyse',
                    'weak_signal' => 'Signal faible', 'maintain' => 'À maintenir', 'test' => 'À tester',
                    'copil_decision' => 'Décision attendue', 'final_objective' => 'Objectif final'
                ] as $key => $label)
                    <label class="field"><span>{{ $label }}</span><textarea class="textarea" name="summary[{{ $key }}]" @disabled(!$canEdit)>{{ old("summary.$key", $report->summary[$key] ?? '') }}</textarea></label>
                @endforeach
            </div>
            <div class="field-grid mt-4 lg:grid-cols-3">
                <label class="field"><span>Canal prioritaire</span><select class="select" name="summary[priority_channel]" @disabled(!$canEdit)>@foreach($report->channels as $channel)<option value="{{ $channel['id'] }}" @selected(($report->summary['priority_channel'] ?? '') === $channel['id'])>{{ $channel['label'] }}</option>@endforeach</select></label>
                <label class="field"><span>Budget alloué (Ar)</span><input class="input" type="number" min="0" step="1000" name="summary[budget_allocated]" value="{{ old('summary.budget_allocated', $report->summary['budget_allocated'] ?? 0) }}" @disabled(!$canEdit)></label>
                <label class="field"><span>Dépenses (Ar)</span><input class="input" type="number" min="0" step="1000" name="summary[budget_spent]" value="{{ old('summary.budget_spent', $report->summary['budget_spent'] ?? 0) }}" @disabled(!$canEdit)></label>
            </div>
            <div class="field-grid mt-4">
                <label class="field"><span>Note budgétaire</span><textarea class="textarea" name="summary[budget_note]" @disabled(!$canEdit)>{{ old('summary.budget_note', $report->summary['budget_note'] ?? '') }}</textarea></label>
                <label class="field"><span>Commentaires</span><textarea class="textarea" name="summary[comments]" @disabled(!$canEdit)>{{ old('summary.comments', $report->summary['comments'] ?? '') }}</textarea></label>
            </div>
        </div>
        <div class="surface">
            <div class="surface-title"><div><h3>Points bloquants</h3><p>Jusqu’à cinq points à arbitrer.</p></div></div>
            <div class="grid gap-3 md:grid-cols-2">
                @for($i = 0; $i < max(2, count($report->summary['blockers'] ?? [])); $i++)
                    <label class="field"><span>Blocage {{ $i + 1 }}</span><input class="input" name="summary[blockers][{{ $i }}]" value="{{ old("summary.blockers.$i", $report->summary['blockers'][$i] ?? '') }}" @disabled(!$canEdit)></label>
                @endfor
            </div>
        </div>
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer la synthèse</button></div>@endif
    </form>

    <div class="surface"><x-attachment-uploader :report="$report" category="annexes" panel="dashboard" title="Annexes du COPIL" description="PDF, PowerPoint, tableaux ou éléments justificatifs." :items="$attachments->get('annexes', collect())" :can-edit="$canEdit" /></div>
</section>
