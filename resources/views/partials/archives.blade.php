<section class="panel" data-panel="archives" hidden>
    <header class="panel-head"><div><p class="eyebrow">Historique</p><h2>Archives mensuelles</h2><p>Chaque mois est conservé, consultable et automatiquement comparé à son M-1.</p></div></header>
    @if(auth()->user()->isAdmin())
        <div class="surface">
            <div class="surface-title"><div><h3>Créer une période</h3><p>Les canaux sont préparés automatiquement avec des valeurs remises à zéro.</p></div></div>
            <form method="POST" action="{{ route('periods.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">@csrf<label class="field flex-1"><span>Nouveau mois</span><input class="input" type="month" name="month" required></label><button class="btn btn-primary"><i data-lucide="calendar-plus"></i>Créer le mois</button></form>
        </div>
    @endif
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Période</th><th>Statut</th><th>Leads</th><th>Avancement</th><th>Dernière mise à jour</th><th>Accès</th><th>Administration</th></tr></thead>
            <tbody>
                @foreach($periods as $archive)
                    @php $archiveReport = $archive->report; $archiveLeads = $archiveReport ? array_sum($calculator->leadsByChannel($archiveReport)) : 0; @endphp
                    <tr>
                        <td><strong>{{ $archive->label }}</strong></td>
                        <td><span class="tag {{ $archive->isClosed() ? 'tag-closed' : 'tag-open' }}">{{ $archive->isClosed() ? 'Clôturé' : 'Ouvert' }}</span></td>
                        <td>{{ number_format($archiveLeads, 0, ',', ' ') }}</td>
                        <td>{{ $archiveReport?->status ?? 'Non créé' }}</td>
                        <td>{{ $archiveReport?->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><a class="btn btn-secondary" href="{{ route('copil.index', ['period' => $archive->key]) }}"><i data-lucide="eye"></i>Consulter</a></td>
                        <td>
                            @if(auth()->user()->isAdmin())
                                @if($archive->isClosed())
                                    <form method="POST" action="{{ route('periods.reopen', $archive) }}">@csrf<button class="btn btn-secondary"><i data-lucide="lock-open"></i>Rouvrir</button></form>
                                @else
                                    <form method="POST" action="{{ route('periods.close', $archive) }}" onsubmit="return confirm('Clôturer et verrouiller ce COPIL ?')">@csrf<button class="btn btn-primary"><i data-lucide="lock-keyhole"></i>Clôturer</button></form>
                                @endif
                            @else — @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
