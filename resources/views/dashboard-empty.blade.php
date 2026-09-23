@extends('layouts.app')

@section('title', 'COPIL Communication')

@php
$nav = [['dashboard', 'layout-dashboard', 'Synthèse']];
if (auth()->user()->isAdmin()) $nav[] = ['users', 'users', 'Utilisateurs'];
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
                <div class="page-title"><p>COPIL Communication</p><h1>Aucune période</h1></div>
            </div>
        </header>

        <main class="content-wrap">
            <select class="mobile-nav" data-mobile-nav aria-label="Section">
                @foreach($nav as [$id, $icon, $label])<option value="{{ $id }}">{{ $label }}</option>@endforeach
            </select>

            @if(session('success'))<div class="alert alert-success" data-flash><i data-lucide="circle-check"></i>{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-error"><i data-lucide="circle-alert"></i><div><strong>Enregistrement impossible.</strong><ul class="mt-1 list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif

            <section class="panel" data-panel="dashboard">
                <header class="panel-head"><div><p class="eyebrow">Mise en service</p><h2>Le premier mois n’est pas encore créé.</h2><p>La connexion fonctionne. Il manque seulement une période mensuelle pour afficher le reporting COPIL.</p></div></header>
                @if(auth()->user()->isAdmin())
                    <div class="surface">
                        <div class="surface-title"><div><h3>Créer la première période</h3><p>Les canaux sont préparés automatiquement avec des valeurs remises à zéro.</p></div></div>
                        <form method="POST" action="{{ route('periods.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">@csrf<label class="field flex-1"><span>Nouveau mois</span><input class="input" type="month" name="month" value="{{ now()->format('Y-m') }}" required></label><button class="btn btn-primary"><i data-lucide="calendar-plus"></i>Créer le mois</button></form>
                    </div>
                @else
                    <div class="status-note"><strong>Aucune période COPIL n’est configurée.</strong> Demandez à un administrateur de créer le premier mois.</div>
                @endif
            </section>

            @if(auth()->user()->isAdmin()) @include('partials.users') @endif
        </main>
    </div>
</div>
@endsection
