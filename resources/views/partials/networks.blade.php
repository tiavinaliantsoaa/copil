<section class="panel" data-panel="networks" hidden>
    <header class="panel-head"><div><p class="eyebrow">Reporting réseaux sociaux</p><h2>Performance par plateforme</h2><p>Chaque valeur affiche automatiquement sa différence par rapport au mois précédent.</p></div></header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'channels']) }}" class="space-y-5">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="networks">
        <div class="platform-tabs" data-platform-tabs="networks">
            @foreach($report->channels as $channel)
                <button class="platform-tab" type="button" data-platform-tab="{{ $channel['id'] }}" style="color: var(--{{ $channel['id'] }}, #17191c)"><span class="platform-dot" style="background: {{ ['facebook'=>'#1877F2','instagram'=>'#E4405F','tiktok'=>'#111111','linkedin'=>'#0A66C2','website'=>'#34A853','email'=>'#EA4335','sms'=>'#F4B400'][$channel['id']] ?? '#17191c' }}"></span>{{ $channel['label'] }}</button>
            @endforeach
        </div>
        @foreach($report->channels as $channelIndex => $channel)
            <div class="platform-panel" data-platform-group="networks" data-platform-id="{{ $channel['id'] }}" hidden>
                <input type="hidden" name="channels[{{ $channelIndex }}][id]" value="{{ $channel['id'] }}">
                <input type="hidden" name="channels[{{ $channelIndex }}][label]" value="{{ $channel['label'] }}">
                <div class="surface">
                    <div class="surface-title"><div><h3>{{ $channel['label'] }}</h3><p>{{ $channel['objective'] }}</p></div><span class="tag tag-red">M-1 automatique</span></div>
                    <label class="field mb-4"><span>Objectif du canal</span><input class="input" name="channels[{{ $channelIndex }}][objective]" value="{{ $channel['objective'] }}" @disabled(!$canEdit)></label>
                    <div class="metric-grid">
                        @foreach($channel['metrics'] as $metricIndex => $metric)
                            @php
                                $currentValue = (float)($metric['value'] ?? 0);
                                $previousValue = $calculator->metricValue($previous, $channel['id'], $metric['id']);
                                $delta = $calculator->delta($currentValue, $previousValue);
                            @endphp
                            <div class="metric-input">
                                <label>{{ $metric['label'] }}</label>
                                @foreach(['id','label','group','unit'] as $hidden)<input type="hidden" name="channels[{{ $channelIndex }}][metrics][{{ $metricIndex }}][{{ $hidden }}]" value="{{ $metric[$hidden] }}">@endforeach
                                <input type="number" min="0" step="0.01" name="channels[{{ $channelIndex }}][metrics][{{ $metricIndex }}][value]" value="{{ $metric['value'] }}" @disabled(!$canEdit)>
                                <small class="{{ $delta['value'] < 0 ? 'text-red-600' : 'text-emerald-700' }}">{{ $delta['value'] > 0 ? '+' : '' }}{{ number_format($delta['value'], $metric['unit'] === 'percent' ? 1 : 0, ',', ' ') }}{{ $metric['unit'] === 'percent' ? ' pt' : ' · '.($delta['percent'] > 0 ? '+' : '').number_format($delta['percent'], 1, ',', ' ').' %' }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="grid-2">
                    @foreach(['performance'=>'Performance du mois','analysis'=>'Analyse','next_action'=>'Prochaine action','decision'=>'Décision / arbitrage'] as $key => $label)
                        <label class="field surface"><span>{{ $label }}</span><textarea class="textarea" name="channels[{{ $channelIndex }}][{{ $key }}]" @disabled(!$canEdit)>{{ $channel[$key] ?? '' }}</textarea></label>
                    @endforeach
                </div>
                @foreach(['best_title','best_reason','improvement_title','improvement_reason','keep','test'] as $contentKey)<input type="hidden" name="channels[{{ $channelIndex }}][content][{{ $contentKey }}]" value="{{ $channel['content'][$contentKey] ?? '' }}">@endforeach
            </div>
        @endforeach
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer les plateformes</button></div>@endif
    </form>
    <div class="grid-2">
        @foreach($report->channels as $channel)
            <div class="surface"><x-attachment-uploader :report="$report" category="evidence-{{ $channel['id'] }}" panel="networks" title="Preuves {{ $channel['label'] }}" description="Captures des statistiques ou exports de la plateforme." :items="$attachments->get('evidence-'.$channel['id'], collect())" :can-edit="$canEdit" /></div>
        @endforeach
    </div>
</section>
