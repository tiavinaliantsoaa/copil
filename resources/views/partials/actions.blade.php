<section class="panel" data-panel="actions" hidden>
    <header class="panel-head"><div><p class="eyebrow">Suivi</p><h2>Actions & décisions</h2><p>Propriétaire, échéance, priorité, indicateur attendu et statut.</p></div>@if($canEdit)<button class="btn btn-secondary" type="button" data-add-list="actions" data-template="action-template"><i data-lucide="plus"></i>Nouvelle action</button>@endif</header>
    <form method="POST" action="{{ route('reports.sections.update', [$report, 'actions']) }}" class="space-y-4">
        @csrf @method('PUT')<input type="hidden" name="_panel" value="actions">
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Priorité</th><th>Action / décision</th><th>Propriétaire</th><th>Échéance</th><th>KPI attendu</th><th>Statut</th><th></th></tr></thead>
                <tbody data-list="actions">
                    @foreach($report->action_plan as $index => $action)
                        <tr data-list-item>
                            <td><input type="hidden" name="action_plan[{{ $index }}][id]" value="{{ $action['id'] }}"><select class="select min-w-[110px]" name="action_plan[{{ $index }}][priority]" @disabled(!$canEdit)>@foreach(['Haute','Moyenne','Basse'] as $priority)<option @selected(($action['priority'] ?? '') === $priority)>{{ $priority }}</option>@endforeach</select></td>
                            <td><textarea class="textarea min-w-[260px]" name="action_plan[{{ $index }}][action]" @disabled(!$canEdit)>{{ $action['action'] ?? '' }}</textarea></td>
                            <td><input class="input min-w-[160px]" name="action_plan[{{ $index }}][owner]" value="{{ $action['owner'] ?? '' }}" @disabled(!$canEdit)></td>
                            <td><input class="input min-w-[145px]" type="date" name="action_plan[{{ $index }}][due_date]" value="{{ $action['due_date'] ?? '' }}" @disabled(!$canEdit)></td>
                            <td><input class="input min-w-[190px]" name="action_plan[{{ $index }}][expected_kpi]" value="{{ $action['expected_kpi'] ?? '' }}" @disabled(!$canEdit)></td>
                            <td><select class="select min-w-[120px]" name="action_plan[{{ $index }}][status]" @disabled(!$canEdit)>@foreach(['À faire','En cours','Bloqué','Terminé'] as $status)<option @selected(($action['status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></td>
                            <td>@if($canEdit)<button class="btn btn-danger btn-icon" type="button" data-remove-item><i data-lucide="trash-2"></i></button>@endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($canEdit)<div class="flex justify-end"><button class="btn btn-primary"><i data-lucide="save"></i>Enregistrer le plan d’action</button></div>@endif
    </form>
</section>

<template id="action-template">
    <tr data-list-item>
        <td><input type="hidden" name="action_plan[__INDEX__][id]" value="__UUID__"><select class="select min-w-[110px]" name="action_plan[__INDEX__][priority]"><option>Haute</option><option selected>Moyenne</option><option>Basse</option></select></td><td><textarea class="textarea min-w-[260px]" name="action_plan[__INDEX__][action]"></textarea></td><td><input class="input min-w-[160px]" name="action_plan[__INDEX__][owner]"></td><td><input class="input min-w-[145px]" type="date" name="action_plan[__INDEX__][due_date]"></td><td><input class="input min-w-[190px]" name="action_plan[__INDEX__][expected_kpi]"></td><td><select class="select min-w-[120px]" name="action_plan[__INDEX__][status]"><option>À faire</option><option>En cours</option><option>Bloqué</option><option>Terminé</option></select></td><td><button class="btn btn-danger btn-icon" type="button" data-remove-item><i data-lucide="trash-2"></i></button></td>
    </tr>
</template>
