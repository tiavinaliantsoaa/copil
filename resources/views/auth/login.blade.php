@extends('layouts.app')

@section('title', 'Connexion · ESCM COPIL')

@section('body')
<main class="login-page">
    <section class="login-visual">
        <img src="{{ asset('images/copil-cover.png') }}" alt="Salle de réunion ESCM">
        <div class="login-copy">
            <p class="mb-3 text-xs font-bold uppercase text-red-300">Reporting mensuel</p>
            <h1>Le COPIL Communication, centralisé.</h1>
            <p class="mt-4 max-w-xl text-base text-white/80">Résultats, contenus, projets et décisions dans un espace privé.</p>
        </div>
    </section>
    <section class="login-form-wrap">
        <form method="POST" action="{{ route('login.store') }}" class="login-form">
            @csrf
            <img src="{{ asset('images/escm-logo.png') }}" alt="ESCM Business School">
            <p class="eyebrow">Espace privé</p>
            <h2>Connexion</h2>
            <p class="mt-2 text-sm text-zinc-500">Utilisez le compte créé par l’administrateur ESCM.</p>

            @if ($errors->any())
                <div class="alert alert-error mt-6">{{ $errors->first() }}</div>
            @endif

            <div class="mt-8 space-y-5">
                <label class="field">
                    <span>Adresse email</span>
                    <input class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </label>
                <label class="field">
                    <span>Mot de passe</span>
                    <input class="input" type="password" name="password" autocomplete="current-password" required>
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-600">
                    <input class="rounded border-zinc-300 text-escm-600 focus:ring-escm-500" type="checkbox" name="remember" value="1">
                    Rester connecté
                </label>
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="log-in"></i> Se connecter
                </button>
            </div>
            <p class="mt-10 text-xs text-zinc-400">ESCM Business School · Accès sécurisé</p>
        </form>
    </section>
</main>
@endsection
