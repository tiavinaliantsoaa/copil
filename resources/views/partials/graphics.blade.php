<section class="panel" data-panel="graphics" hidden>
    <header class="panel-head"><div><p class="eyebrow">Graphisme & visuels</p><h2>Production créative du mois</h2><p>Suivi de la production et galerie élargie avec aperçu immédiat des créations.</p></div></header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'graphics']) }}" class="surface">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="graphics">
        <div class="field-grid lg:grid-cols-4">
            <label class="field"><span>Visuels produits</span><input class="input" type="number" min="0" name="graphics[visuals_produced]" value="{{ $report->graphics['visuals_produced'] ?? 0 }}" @disabled(!$canEdit)></label>
            <label class="field"><span>Campagnes créées</span><input class="input" type="number" min="0" name="graphics[campaigns_created]" value="{{ $report->graphics['campaigns_created'] ?? 0 }}" @disabled(!$canEdit)></label>
            <label class="field"><span>Délai moyen (jours)</span><input class="input" type="number" min="0" step="0.1" name="graphics[average_delivery_days]" value="{{ $report->graphics['average_delivery_days'] ?? 0 }}" @disabled(!$canEdit)></label>
            <label class="field"><span>Révisions majeures</span><input class="input" type="number" min="0" name="graphics[major_revisions]" value="{{ $report->graphics['major_revisions'] ?? 0 }}" @disabled(!$canEdit)></label>
        </div>
        <label class="field mt-4"><span>Analyse de la production</span><textarea class="textarea" name="graphics[note]" @disabled(!$canEdit)>{{ $report->graphics['note'] ?? '' }}</textarea></label>
        @if($canEdit)<div class="mt-5 flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer</button></div>@endif
    </form>
    <div class="surface"><x-attachment-uploader :report="$report" category="graphics" panel="graphics" title="Visuels du mois" description="Les images apparaissent dans la galerie dès leur envoi." :items="$attachments->get('graphics', collect())" :can-edit="$canEdit" /></div>
</section>
