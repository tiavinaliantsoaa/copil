@extends('layouts.app')

@section('title', $period->label.' · ESCM COPIL')

@php
$nav = [
    ['dashboard', 'layout-dashboard', 'Synthèse'],
    ['networks', 'chart-no-axes-combined', 'Réseaux sociaux'],
    ['contents', 'panels-top-left', 'Performance contenus'],
    ['graphics', 'images', 'Graphisme & visuels'],
    ['training', 'graduation-cap', 'Formation pro'],
    ['tools', 'cpu', 'Informatique & outils'],
    ['events', 'calendar-days', 'Événements'],
    ['projects', 'briefcase-business', 'Projets'],
    ['actions', 'list-checks', 'Actions & décisions'],
    ['archives', 'archive', 'Archives'],
];
if(auth()->user()->isAdmin()) $nav[] = ['users', 'users', 'Utilisateurs'];
@endphp

@section('body')
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-lockup"><img src="{{ asset('images/escm-logo.png') }}" alt="ESCM"></div>
        <nav class="nav-list" aria-label="Navigation COPIL">
            @foreach($nav as [$id, $icon, $label])
                <button type="button" class="nav-button" data-nav="{{ $id }}"><i data-lucide="{{ $icon }}"></i>{{ $label }}</button>
            @endforeach
        </nav>
        <div class="sidebar-user">
            <strong class="block truncate text-ink">{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->roleLabel() }}</span>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button class="flex items-center gap-2 font-semibold text-zinc-600 hover:text-escm-600"><i data-lucide="log-out" class="h-4 w-4"></i>Déconnexion</button></form>
        </div>
    </aside>

    <div class="main-column">
        <header class="topbar">
            <div class="topbar-inner">
                <img class="mobile-logo" src="{{ asset('images/escm-logo.png') }}" alt="ESCM">
                <div class="page-title"><p>COPIL Communication</p><h1>{{ $period->label }}</h1></div>
                <label class="sr-only" for="period-select">Période</label>
                <select id="period-select" class="select w-auto min-w-[170px]" data-period-select data-url="{{ route('copil.index') }}">
                    @foreach($periods as $item)<option value="{{ $item->key }}" @selected($item->id === $period->id)>{{ $item->label }}{{ $item->isClosed() ? ' · clôturé' : '' }}</option>@endforeach
                </select>
                <a class="btn btn-secondary" href="{{ route('exports.pdf', $period) }}"><i data-lucide="file-down"></i><span class="hidden sm:inline">PDF</span></a>
                <a class="btn btn-secondary" href="{{ route('exports.pptx', $period) }}"><i data-lucide="presentation"></i><span class="hidden sm:inline">PPTX</span></a>
                <a class="btn btn-primary" href="{{ route('presentation', $period) }}" target="_blank"><i data-lucide="monitor-play"></i><span class="hidden sm:inline">Présenter</span></a>
            </div>
        </header>

        <main class="content-wrap">
            <select class="mobile-nav" data-mobile-nav aria-label="Section">
                @foreach($nav as [$id, $icon, $label])<option value="{{ $id }}">{{ $label }}</option>@endforeach
            </select>

            @if(session('success'))<div class="alert alert-success" data-flash><i data-lucide="circle-check"></i>{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-error"><i data-lucide="circle-alert"></i><div><strong>Enregistrement impossible.</strong><ul class="mt-1 list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
            @if($period->isClosed())<div class="status-note mb-5"><strong>Mois clôturé.</strong> Les données sont verrouillées{{ auth()->user()->isAdmin() ? ', mais un administrateur peut les corriger ou rouvrir la période.' : '.' }}</div>@endif

            @include('partials.dashboard')
            @include('partials.networks')
            @include('partials.contents')
            @include('partials.graphics')
            @include('partials.training')
            @include('partials.tools')
            @include('partials.events')
            @include('partials.projects')
            @include('partials.actions')
            @include('partials.archives')
            @if(auth()->user()->isAdmin()) @include('partials.users') @endif
        </main>
    </div>
</div>
@endsection

@php
$escmCharts = [
    'platformLabels' => collect($report->channels)->pluck('label')->values(),
    'leads' => collect($report->channels)->map(fn ($channel) => $stats['leads'][$channel['id']] ?? 0)->values(),
    'previousLeads' => collect($report->channels)->map(fn ($channel) => $previous ? $calculator->metricValue($previous, $channel['id'], 'leads') : 0)->values(),
    'currentLabel' => $period->label,
    'previousLabel' => $previousPeriod?->label ?? 'M-1',
    'trainingLabels' => collect($report->training['channels'] ?? [])->pluck('label')->values(),
    'trainingLeads' => collect($report->training['channels'] ?? [])->pluck('leads')->values(),
];
@endphp

@push('scripts')
<script>
window.escmCharts = @json($escmCharts);
</script>
@endpush
