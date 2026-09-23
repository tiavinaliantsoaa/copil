<section class="panel" data-panel="events" hidden>
    <header class="panel-head"><div><p class="eyebrow">Événements</p><h2>Activations du mois</h2><p>Objectifs, production de contenus, résultats et prochaines étapes.</p></div>@if($canEdit)<button class="btn btn-secondary" type="button" data-add-list="events" data-template="event-template"><i data-lucide="plus"></i>Nouvel événement</button>@endif</header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'events']) }}" class="space-y-4">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="events">
        <div class="grid gap-4 lg:grid-cols-2" data-list="events">
            @foreach($report->events as $index => $event)
                <article class="list-item space-y-4" data-list-item>
                    <input type="hidden" name="events[{{ $index }}][id]" value="{{ $event['id'] }}">
                    <div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="events[{{ $index }}][name]" value="{{ $event['name'] }}" @disabled(!$canEdit)>@if($canEdit)<button class="btn btn-danger btn-icon" type="button" data-remove-item title="Supprimer"><i data-lucide="trash-2"></i></button>@endif</div>
                    <label class="field"><span>Objectif</span><input class="input" name="events[{{ $index }}][objective]" value="{{ $event['objective'] ?? '' }}" @disabled(!$canEdit)></label>
                    <div class="field-grid"><label class="field"><span>Photos</span><input class="input" type="number" min="0" name="events[{{ $index }}][photos]" value="{{ $event['photos'] ?? 0 }}" @disabled(!$canEdit)></label><label class="field"><span>Reels / vidéos</span><input class="input" type="number" min="0" name="events[{{ $index }}][reels]" value="{{ $event['reels'] ?? 0 }}" @disabled(!$canEdit)></label></div>
                    <label class="field"><span>Résultats</span><textarea class="textarea" name="events[{{ $index }}][results]" @disabled(!$canEdit)>{{ $event['results'] ?? '' }}</textarea></label>
                    <label class="field"><span>Prochaine étape</span><textarea class="textarea" name="events[{{ $index }}][next_step]" @disabled(!$canEdit)>{{ $event['next_step'] ?? '' }}</textarea></label>
                </article>
            @endforeach
        </div>
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer les événements</button></div>@endif
    </form>
    @foreach($report->events as $event)<div class="surface"><x-attachment-uploader :report="$report" category="event-{{ $event['id'] }}" panel="events" title="Visuels · {{ $event['name'] }}" :items="$attachments->get('event-'.$event['id'], collect())" :can-edit="$canEdit" /></div>@endforeach
</section>

<template id="event-template">
    <article class="list-item space-y-4" data-list-item>
        <input type="hidden" name="events[__INDEX__][id]" value="__UUID__">
        <div class="flex items-center gap-3"><input class="input flex-1 font-bold" name="events[__INDEX__][name]" placeholder="Nom de l’événement" required><button class="btn btn-danger btn-icon" type="button" data-remove-item><i data-lucide="trash-2"></i></button></div>
        <label class="field"><span>Objectif</span><input class="input" name="events[__INDEX__][objective]"></label>
        <div class="field-grid"><label class="field"><span>Photos</span><input class="input" type="number" min="0" value="0" name="events[__INDEX__][photos]"></label><label class="field"><span>Reels / vidéos</span><input class="input" type="number" min="0" value="0" name="events[__INDEX__][reels]"></label></div>
        <label class="field"><span>Résultats</span><textarea class="textarea" name="events[__INDEX__][results]"></textarea></label><label class="field"><span>Prochaine étape</span><textarea class="textarea" name="events[__INDEX__][next_step]"></textarea></label>
    </article>
</template>
