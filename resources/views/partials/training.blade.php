<section class="panel" data-panel="training" hidden>
    <header class="panel-head">
        <div><p class="eyebrow">Formation professionnelle</p><h2>Performance Formation Pro</h2><p>Audience, leads, conversion et évolution M-1 calculés automatiquement.</p></div>
        <img class="h-14 w-auto object-contain" src="{{ asset('images/fp-logo.png') }}" alt="ESCM Formation professionnelle">
    </header>
    <div class="kpi-grid">
        <article class="kpi"><p class="kpi-label">Audience totale</p><p class="kpi-value">{{ number_format($stats['training_audience'], 0, ',', ' ') }}</p><p class="delta {{ $stats['training_audience_delta']['value'] < 0 ? 'negative' : '' }}">{{ $stats['training_audience_delta']['value'] > 0 ? '+' : '' }}{{ number_format($stats['training_audience_delta']['percent'], 1, ',', ' ') }} % vs M-1</p></article>
        <article class="kpi"><p class="kpi-label">Leads Formation Pro</p><p class="kpi-value">{{ number_format($stats['training_leads'], 0, ',', ' ') }}</p><p class="delta {{ $stats['training_leads_delta']['value'] < 0 ? 'negative' : '' }}">{{ $stats['training_leads_delta']['value'] > 0 ? '+' : '' }}{{ number_format($stats['training_leads_delta']['percent'], 1, ',', ' ') }} % vs M-1</p></article>
        <article class="kpi"><p class="kpi-label">Taux de conversion</p><p class="kpi-value">{{ number_format($stats['training_conversion'], 2, ',', ' ') }} %</p><p class="mt-2 text-xs font-semibold text-zinc-500">Leads / audience</p></article>
        <article class="kpi"><p class="kpi-label">Demandes entrantes</p><p class="kpi-value">{{ number_format((float)($report->training['inbound_requests'] ?? 0), 0, ',', ' ') }}</p><p class="mt-2 text-xs font-semibold text-zinc-500">Renseignées ce mois</p></article>
    </div>
    <div class="grid-2">
        <article class="surface"><div class="surface-title"><div><h3>Répartition des leads FP</h3><p>Contribution par canal.</p></div><span class="tag tag-red">Camembert</span></div><div class="chart-box"><canvas id="trainingChart"></canvas></div></article>
        <article class="surface"><div class="surface-title"><div><h3>Lecture du mois</h3><p>Éléments à présenter en comité.</p></div></div><dl class="space-y-4 text-sm"><div><dt class="font-bold text-zinc-500">Formation à mettre en avant</dt><dd class="mt-1 text-ink">{{ $report->training['featured_training'] ?: 'À compléter' }}</dd></div><div><dt class="font-bold text-zinc-500">Canal prioritaire</dt><dd class="mt-1 text-ink">{{ $report->training['priority_channel'] ?: 'À compléter' }}</dd></div><div><dt class="font-bold text-zinc-500">Décision attendue</dt><dd class="mt-1 text-ink">{{ $report->training['decision'] ?: 'À compléter' }}</dd></div></dl></article>
    </div>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'training']) }}" class="space-y-5">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="training">
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Canal</th><th>Objectif</th><th>Audience</th><th>Leads</th><th>Prochaine action</th></tr></thead>
                <tbody>
                    @foreach($report->training['channels'] as $index => $channel)
                        <tr>
                            <td><input type="hidden" name="training[channels][{{ $index }}][id]" value="{{ $channel['id'] }}"><input class="input min-w-[130px]" name="training[channels][{{ $index }}][label]" value="{{ $channel['label'] }}" @disabled(!$canEdit)></td>
                            <td><input class="input min-w-[180px]" name="training[channels][{{ $index }}][objective]" value="{{ $channel['objective'] }}" @disabled(!$canEdit)></td>
                            <td><input class="input w-28" type="number" min="0" name="training[channels][{{ $index }}][value]" value="{{ $channel['value'] }}" @disabled(!$canEdit)></td>
                            <td><input class="input w-24" type="number" min="0" name="training[channels][{{ $index }}][leads]" value="{{ $channel['leads'] }}" @disabled(!$canEdit)></td>
                            <td><input class="input min-w-[240px]" name="training[channels][{{ $index }}][action]" value="{{ $channel['action'] }}" @disabled(!$canEdit)></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="surface">
            <div class="field-grid lg:grid-cols-4">
                @foreach(['impressions'=>'Impressions','interactions'=>'Interactions','clicks'=>'Clics','inbound_requests'=>'Demandes entrantes'] as $key=>$label)<label class="field"><span>{{ $label }}</span><input class="input" type="number" min="0" name="training[{{ $key }}]" value="{{ $report->training[$key] ?? 0 }}" @disabled(!$canEdit)></label>@endforeach
            </div>
            <div class="field-grid mt-4">
                @foreach(['format_to_keep'=>'Format à conserver','strong_message'=>'Message fort','offer_to_promote'=>'Offre à promouvoir','priority_audience'=>'Audience prioritaire','cta_to_test'=>'CTA à tester','testimonial'=>'Témoignage','featured_training'=>'Formation mise en avant','priority_channel'=>'Canal prioritaire','decision'=>'Décision COPIL'] as $key=>$label)<label class="field"><span>{{ $label }}</span><textarea class="textarea" name="training[{{ $key }}]" @disabled(!$canEdit)>{{ $report->training[$key] ?? '' }}</textarea></label>@endforeach
            </div>
            @if($canEdit)<div class="mt-5 flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer Formation Pro</button></div>@endif
        </div>
    </form>
    <div class="surface"><x-attachment-uploader :report="$report" category="training" panel="training" title="Visuels et preuves Formation Pro" description="Résultats, meilleurs contenus, offres et captures de campagne." :items="$attachments->get('training', collect())" :can-edit="$canEdit" /></div>
</section>
