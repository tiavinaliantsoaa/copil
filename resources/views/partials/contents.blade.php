<section class="panel" data-panel="contents" hidden>
    <header class="panel-head"><div><p class="eyebrow">Performance contenus</p><h2>Meilleur contenu vs contenu à améliorer</h2><p>Une lecture côte à côte, appliquée séparément à chaque plateforme, avec aperçu des visuels.</p></div></header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'channels']) }}" class="space-y-5">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="contents">
        <div class="platform-tabs" data-platform-tabs="contents">
            @foreach($report->channels as $channel)<button class="platform-tab" type="button" data-platform-tab="{{ $channel['id'] }}"><span class="platform-dot" style="background: {{ ['facebook'=>'#1877F2','instagram'=>'#E4405F','tiktok'=>'#111111','linkedin'=>'#0A66C2','website'=>'#34A853','email'=>'#EA4335','sms'=>'#F4B400'][$channel['id']] ?? '#17191c' }}"></span>{{ $channel['label'] }}</button>@endforeach
        </div>
        @foreach($report->channels as $channelIndex => $channel)
            <div class="platform-panel" data-platform-group="contents" data-platform-id="{{ $channel['id'] }}" hidden>
                @foreach(['id','label','objective','performance','analysis','next_action','decision'] as $key)<input type="hidden" name="channels[{{ $channelIndex }}][{{ $key }}]" value="{{ $channel[$key] ?? '' }}">@endforeach
                @foreach($channel['metrics'] as $metricIndex => $metric)@foreach(['id','label','value','group','unit'] as $key)<input type="hidden" name="channels[{{ $channelIndex }}][metrics][{{ $metricIndex }}][{{ $key }}]" value="{{ $metric[$key] }}">@endforeach @endforeach
                <div class="grid-2">
                    <article class="surface content-side best">
                        <div class="surface-title"><div><p class="eyebrow text-emerald-700">Meilleur contenu</p><h3>{{ $channel['label'] }}</h3></div><i data-lucide="trending-up" class="text-emerald-600"></i></div>
                        <div class="space-y-4">
                            <label class="field"><span>Titre / campagne</span><input class="input" name="channels[{{ $channelIndex }}][content][best_title]" value="{{ $channel['content']['best_title'] ?? '' }}" @disabled(!$canEdit)></label>
                            <label class="field"><span>Pourquoi cela a fonctionné</span><textarea class="textarea" name="channels[{{ $channelIndex }}][content][best_reason]" @disabled(!$canEdit)>{{ $channel['content']['best_reason'] ?? '' }}</textarea></label>
                            <label class="field"><span>À conserver</span><textarea class="textarea" name="channels[{{ $channelIndex }}][content][keep]" @disabled(!$canEdit)>{{ $channel['content']['keep'] ?? '' }}</textarea></label>
                        </div>
                    </article>
                    <article class="surface content-side improve">
                        <div class="surface-title"><div><p class="eyebrow">Contenu à améliorer</p><h3>{{ $channel['label'] }}</h3></div><i data-lucide="wand-sparkles" class="text-escm-600"></i></div>
                        <div class="space-y-4">
                            <label class="field"><span>Titre / campagne</span><input class="input" name="channels[{{ $channelIndex }}][content][improvement_title]" value="{{ $channel['content']['improvement_title'] ?? '' }}" @disabled(!$canEdit)></label>
                            <label class="field"><span>Pourquoi améliorer</span><textarea class="textarea" name="channels[{{ $channelIndex }}][content][improvement_reason]" @disabled(!$canEdit)>{{ $channel['content']['improvement_reason'] ?? '' }}</textarea></label>
                            <label class="field"><span>Test à mener</span><textarea class="textarea" name="channels[{{ $channelIndex }}][content][test]" @disabled(!$canEdit)>{{ $channel['content']['test'] ?? '' }}</textarea></label>
                        </div>
                    </article>
                </div>
            </div>
        @endforeach
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer l’analyse des contenus</button></div>@endif
    </form>
    @foreach($report->channels as $channel)
        <div class="surface">
            <div class="surface-title"><div><h3>Visuels {{ $channel['label'] }}</h3><p>Les deux créations restent séparées pour faciliter la comparaison.</p></div></div>
            <div class="grid-2">
                <x-attachment-uploader :report="$report" category="content-{{ $channel['id'] }}-best" panel="contents" title="Meilleur contenu" :items="$attachments->get('content-'.$channel['id'].'-best', collect())" :can-edit="$canEdit" />
                <x-attachment-uploader :report="$report" category="content-{{ $channel['id'] }}-improvement" panel="contents" title="Contenu à améliorer" :items="$attachments->get('content-'.$channel['id'].'-improvement', collect())" :can-edit="$canEdit" />
            </div>
        </div>
    @endforeach
</section>
