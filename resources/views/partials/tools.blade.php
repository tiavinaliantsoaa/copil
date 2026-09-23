<section class="panel" data-panel="tools" hidden>
    <header class="panel-head"><div><p class="eyebrow">Informatique & outils</p><h2>Projets et solutions numériques</h2><p>Ajoutez, clôturez ou retirez un projet, puis documentez-le avec des captures d’écran.</p></div>@if($canEdit)<button class="btn btn-secondary" type="button" data-add-list="tools" data-template="tool-template"><i data-lucide="plus"></i>Nouveau projet</button>@endif</header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'tools']) }}" class="space-y-4">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="tools">
        <div class="grid gap-4 lg:grid-cols-2" data-list="tools">
            @foreach($report->tools as $index => $tool)
                <article class="list-item space-y-4" data-list-item>
                    <input type="hidden" name="tools[{{ $index }}][id]" value="{{ $tool['id'] }}">
                    <div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="tools[{{ $index }}][name]" value="{{ $tool['name'] }}" @disabled(!$canEdit)>@if($canEdit)<button class="btn btn-danger btn-icon" type="button" data-remove-item title="Supprimer"><i data-lucide="trash-2"></i></button>@endif</div>
                    <div class="field-grid">
                        <label class="field"><span>Statut</span><select class="select" name="tools[{{ $index }}][status]" @disabled(!$canEdit)>@foreach(['À faire','En cours','Bloqué','Terminé'] as $status)<option @selected(($tool['status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></label>
                        <label class="field"><span>Avancement (%)</span><input class="input" type="number" min="0" max="100" name="tools[{{ $index }}][progress]" value="{{ $tool['progress'] ?? 0 }}" @disabled(!$canEdit)></label>
                    </div>
                    @foreach(['blocker'=>'Point bloquant','decision'=>'Décision attendue','next_milestone'=>'Prochain jalon'] as $key=>$label)<label class="field"><span>{{ $label }}</span><textarea class="textarea" name="tools[{{ $index }}][{{ $key }}]" @disabled(!$canEdit)>{{ $tool[$key] ?? '' }}</textarea></label>@endforeach
                    <div class="progress-track"><div class="progress-bar" style="width: {{ min(100, max(0, $tool['progress'] ?? 0)) }}%"></div></div>
                </article>
            @endforeach
        </div>
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer les projets informatiques</button></div>@endif
    </form>
    @forelse($report->tools as $tool)
        <div class="surface"><x-attachment-uploader :report="$report" category="tool-{{ $tool['id'] }}" panel="tools" title="Captures · {{ $tool['name'] }}" description="Maquettes, captures d’écran ou état du projet." :items="$attachments->get('tool-'.$tool['id'], collect())" :can-edit="$canEdit" /></div>
    @empty
        <div class="surface text-sm text-zinc-500">Aucun projet informatique pour ce mois.</div>
    @endforelse
</section>

<template id="tool-template">
    <article class="list-item space-y-4" data-list-item>
        <input type="hidden" name="tools[__INDEX__][id]" value="__UUID__">
        <div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="tools[__INDEX__][name]" placeholder="Nom du projet" required><button class="btn btn-danger btn-icon" type="button" data-remove-item title="Supprimer"><i data-lucide="trash-2"></i></button></div>
        <div class="field-grid"><label class="field"><span>Statut</span><select class="select" name="tools[__INDEX__][status]"><option>À faire</option><option>En cours</option><option>Bloqué</option><option>Terminé</option></select></label><label class="field"><span>Avancement (%)</span><input class="input" type="number" min="0" max="100" name="tools[__INDEX__][progress]" value="0"></label></div>
        <label class="field"><span>Point bloquant</span><textarea class="textarea" name="tools[__INDEX__][blocker]"></textarea></label>
        <label class="field"><span>Décision attendue</span><textarea class="textarea" name="tools[__INDEX__][decision]"></textarea></label>
        <label class="field"><span>Prochain jalon</span><textarea class="textarea" name="tools[__INDEX__][next_milestone]"></textarea></label>
    </article>
</template>
