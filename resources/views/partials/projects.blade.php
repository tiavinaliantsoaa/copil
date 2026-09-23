<section class="panel" data-panel="projects" hidden>
    <header class="panel-head"><div><p class="eyebrow">Projets</p><h2>Projets prioritaires</h2><p>Avancement, risques, responsables et preuves visuelles de chaque chantier.</p></div>@if($canEdit)<button class="btn btn-secondary" type="button" data-add-list="projects" data-template="project-template"><i data-lucide="plus"></i>Nouveau projet</button>@endif</header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'projects']) }}" class="space-y-4">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="projects">
        <div class="grid gap-4 lg:grid-cols-2" data-list="projects">
            @foreach($report->projects as $index => $project)
                <article class="list-item space-y-4" data-list-item>
                    <input type="hidden" name="projects[{{ $index }}][id]" value="{{ $project['id'] }}">
                    <div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="projects[{{ $index }}][name]" value="{{ $project['name'] }}" @disabled(!$canEdit)>@if($canEdit)<button class="btn btn-danger btn-icon" type="button" data-remove-item title="Supprimer"><i data-lucide="trash-2"></i></button>@endif</div>
                    <label class="field"><span>Responsable</span><input class="input" name="projects[{{ $index }}][owner]" value="{{ $project['owner'] ?? '' }}" @disabled(!$canEdit)></label>
                    <div class="field-grid"><label class="field"><span>Statut</span><select class="select" name="projects[{{ $index }}][status]" @disabled(!$canEdit)>@foreach(['À faire','En cours','Bloqué','Terminé'] as $status)<option @selected(($project['status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></label><label class="field"><span>Avancement (%)</span><input class="input" type="number" min="0" max="100" name="projects[{{ $index }}][progress]" value="{{ $project['progress'] ?? 0 }}" @disabled(!$canEdit)></label></div>
                    <label class="field"><span>Risque</span><textarea class="textarea" name="projects[{{ $index }}][risk]" @disabled(!$canEdit)>{{ $project['risk'] ?? '' }}</textarea></label>
                    <label class="field"><span>Prochaine action</span><textarea class="textarea" name="projects[{{ $index }}][next_action]" @disabled(!$canEdit)>{{ $project['next_action'] ?? '' }}</textarea></label>
                    <div class="progress-track"><div class="progress-bar" style="width: {{ min(100, max(0, $project['progress'] ?? 0)) }}%"></div></div>
                </article>
            @endforeach
        </div>
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer les projets</button></div>@endif
    </form>
    @foreach($report->projects as $project)<div class="surface"><x-attachment-uploader :report="$report" category="project-{{ $project['id'] }}" panel="projects" title="Visuels · {{ $project['name'] }}" description="Captures, photos ou maquettes du projet." :items="$attachments->get('project-'.$project['id'], collect())" :can-edit="$canEdit" /></div>@endforeach
</section>

<template id="project-template">
    <article class="list-item space-y-4" data-list-item>
        <input type="hidden" name="projects[__INDEX__][id]" value="__UUID__"><div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="projects[__INDEX__][name]" placeholder="Nom du projet" required><button class="btn btn-danger btn-icon" type="button" data-remove-item><i data-lucide="trash-2"></i></button></div>
        <label class="field"><span>Responsable</span><input class="input" name="projects[__INDEX__][owner]"></label><div class="field-grid"><label class="field"><span>Statut</span><select class="select" name="projects[__INDEX__][status]"><option>À faire</option><option>En cours</option><option>Bloqué</option><option>Terminé</option></select></label><label class="field"><span>Avancement (%)</span><input class="input" type="number" min="0" max="100" value="0" name="projects[__INDEX__][progress]"></label></div><label class="field"><span>Risque</span><textarea class="textarea" name="projects[__INDEX__][risk]"></textarea></label><label class="field"><span>Prochaine action</span><textarea class="textarea" name="projects[__INDEX__][next_action]"></textarea></label>
    </article>
</template>
