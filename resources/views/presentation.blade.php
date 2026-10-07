@extends('layouts.app')

@section('title', 'Présentation · '.$period->label)

@push('head')
<style>
    .presentation-visuals { display: flex; flex: 1 1 auto; min-height: 0; align-items: center; justify-content: center; gap: 1.25rem; margin-top: 1.25rem; }
    .presentation-visuals img { max-height: 100%; max-width: calc(100% / var(--visual-count) - 1rem); width: auto; height: auto; object-fit: contain; }
</style>
@endpush

@section('body')
<main class="presentation-shell">
    <section class="presentation-slide presentation-cover active" data-slide style="background-image: url('{{ asset('images/copil-cover.png') }}')">
        <div class="presentation-content">
            <img class="mb-10 h-20 w-auto self-start object-contain" src="{{ asset('images/escm-logo.png') }}" alt="ESCM">
            <p class="mb-4 text-sm font-bold uppercase text-escm-600">{{ $period->label }}</p>
            <h1>ESCM COPIL <span>Communication</span></h1>
            <p class="mt-6 text-xl font-semibold text-zinc-600">Résultats, contenus, projets et décisions</p>
        </div>
    </section>

    <section class="presentation-slide" data-slide>
        <div class="presentation-content">
            <header class="presentation-title"><p>Sommaire</p><h2>Le reporting en un seul parcours</h2></header>
            <div class="mt-[7vh] grid grid-cols-2 gap-x-16 gap-y-6 text-2xl font-bold">
                @foreach(['01 · Synthèse du mois','02 · Reporting réseaux sociaux','03 · Performance contenus','04 · Graphisme & visuels','05 · Formation professionnelle','06 · Informatique & outils','07 · Événements','08 · Projets & actions'] as $item)<div class="border-b border-zinc-200 pb-5">{{ $item }}</div>@endforeach
            </div>
        </div>
    </section>

    <section class="presentation-slide" data-slide>
        <div class="presentation-content">
            <header class="presentation-title"><p>01 · Synthèse</p><h2>{{ $period->label }} en un regard</h2></header>
            <div class="mt-[5vh] grid grid-cols-4 gap-4">
                <article class="kpi"><p class="kpi-label">Leads</p><p class="kpi-value">{{ number_format($stats['total_leads'], 0, ',', ' ') }}</p><p class="delta">{{ $stats['total_leads_delta']['percent'] > 0 ? '+' : '' }}{{ number_format($stats['total_leads_delta']['percent'], 1, ',', ' ') }} %</p></article>
                <article class="kpi"><p class="kpi-label">Portée</p><p class="kpi-value">{{ number_format($stats['reach'], 0, ',', ' ') }}</p><p class="delta">{{ $stats['reach_delta']['percent'] > 0 ? '+' : '' }}{{ number_format($stats['reach_delta']['percent'], 1, ',', ' ') }} %</p></article>
                <article class="kpi"><p class="kpi-label">Budget consommé</p><p class="kpi-value">{{ number_format($stats['budget_rate'], 1, ',', ' ') }} %</p></article>
                <article class="kpi"><p class="kpi-label">Complétude</p><p class="kpi-value">{{ $stats['completion'] }} %</p></article>
            </div>
            <div class="mt-[5vh] grid grid-cols-2 gap-5">
                <div class="border-l-4 border-emerald-500 bg-zinc-50 p-6"><p class="eyebrow text-emerald-700">Ce qui progresse</p><p class="mt-3 text-xl font-semibold leading-8">{{ $report->summary['progress'] }}</p></div>
                <div class="border-l-4 border-escm-500 bg-zinc-50 p-6"><p class="eyebrow">Point de vigilance</p><p class="mt-3 text-xl font-semibold leading-8">{{ $report->summary['decline'] }}</p></div>
            </div>
            <div class="mt-5 bg-escm-600 p-6 text-white"><p class="text-xs font-bold uppercase">Décision COPIL</p><p class="mt-2 text-2xl font-bold">{{ $report->summary['copil_decision'] }}</p></div>
        </div>
    </section>

    @foreach([
        ['02','Reporting réseaux sociaux'], ['03','Performance des contenus'], ['04','Graphisme & visuels'],
        ['05','Formation professionnelle'], ['06','Informatique & outils'], ['07','Événements'], ['08','Projets & actions']
    ] as [$number, $title])
        <section class="presentation-slide separator" data-slide style="background-image: url('{{ asset('images/copil-cover.png') }}')">
            <div class="presentation-content"><p class="mb-5 text-xl font-bold text-red-300">{{ $number }}</p><h2>{{ $title }}</h2></div>
        </section>

        @if($number === '02')
            <section class="presentation-slide" data-slide><div class="presentation-content">
                <header class="presentation-title"><p>Reporting réseaux sociaux</p><h2>Leads par plateforme</h2></header>
                <div class="mt-[7vh] grid grid-cols-4 gap-4">
                    @foreach($report->channels as $channel)
                        <article class="kpi border-t-4" style="border-top-color: {{ ['facebook'=>'#1877F2','instagram'=>'#E4405F','tiktok'=>'#111111','linkedin'=>'#0A66C2','website'=>'#34A853','email'=>'#EA4335','sms'=>'#F4B400'][$channel['id']] ?? '#d9252a' }}"><p class="kpi-label">{{ $channel['label'] }}</p><p class="kpi-value">{{ number_format($stats['leads'][$channel['id']] ?? 0, 0, ',', ' ') }}</p><p class="mt-2 text-xs font-semibold text-zinc-500">leads / conversions</p></article>
                    @endforeach
                </div>
                <div class="mt-[5vh] grid grid-cols-2 gap-5"><div class="bg-zinc-50 p-6"><p class="eyebrow">Analyse</p><p class="mt-3 text-xl leading-8">{{ $report->summary['analysis'] }}</p></div><div class="bg-zinc-50 p-6"><p class="eyebrow">Objectif final</p><p class="mt-3 text-xl leading-8">{{ $report->summary['final_objective'] }}</p></div></div>
            </div></section>
        @elseif($number === '03')
            @foreach(array_chunk($report->channels, 2) as $channelPair)
                <section class="presentation-slide" data-slide><div class="presentation-content">
                    <header class="presentation-title"><p>Performance contenus</p><h2>{{ collect($channelPair)->pluck('label')->join(' & ') }}</h2></header>
                    <div class="mt-[5vh] grid grid-cols-2 gap-5">
                        @foreach($channelPair as $channel)
                            <article class="grid grid-cols-2 gap-4 bg-zinc-50 p-5">
                                <div><p class="eyebrow text-emerald-700">Meilleur</p><h3 class="mt-2 text-xl font-bold">{{ $channel['content']['best_title'] }}</h3><p class="mt-3 text-sm leading-6 text-zinc-600">{{ $channel['content']['best_reason'] }}</p>@php $best = $attachments->get('content-'.$channel['id'].'-best', collect())->first(fn($file) => $file->isImage()); @endphp @if($best)<img class="mt-4 aspect-video w-full object-cover" src="{{ route('attachments.show', $best) }}" alt="Meilleur contenu">@endif</div>
                                <div><p class="eyebrow">À améliorer</p><h3 class="mt-2 text-xl font-bold">{{ $channel['content']['improvement_title'] }}</h3><p class="mt-3 text-sm leading-6 text-zinc-600">{{ $channel['content']['improvement_reason'] }}</p>@php $improve = $attachments->get('content-'.$channel['id'].'-improvement', collect())->first(); @endphp @if($improve?->isImage())<img class="mt-4 aspect-video w-full object-cover" src="{{ route('attachments.show', $improve) }}" alt="Contenu à améliorer">@endif</div>
                            </article>
                        @endforeach
                    </div>
                </div></section>
            @endforeach
        @elseif($number === '04')
            <section class="presentation-slide" data-slide><div class="presentation-content">
                <header class="presentation-title"><p>Graphisme & visuels</p><h2>Production du mois</h2></header>
                <div class="mt-[5vh] grid grid-cols-4 gap-4">@foreach(['visuals_produced'=>'Visuels produits','campaigns_created'=>'Campagnes','average_delivery_days'=>'Délai moyen (j)','major_revisions'=>'Révisions'] as $key=>$label)<article class="kpi"><p class="kpi-label">{{ $label }}</p><p class="kpi-value">{{ $report->graphics[$key] ?? 0 }}</p></article>@endforeach</div>
                <p class="mt-6 border-l-4 border-escm-500 bg-zinc-50 p-5 text-xl">{{ $report->graphics['note'] }}</p>
                @php $visuals = $attachments->get('graphics', collect())->filter(fn($file) => $file->isImage())->take(4); @endphp
                <div class="presentation-visuals" style="--visual-count: {{ max($visuals->count(), 1) }}">@foreach($visuals as $file)<img src="{{ route('attachments.show', $file) }}" alt="{{ $file->original_name }}">@endforeach</div>
            </div></section>
        @elseif($number === '05')
            <section class="presentation-slide" data-slide><div class="presentation-content">
                <header class="presentation-title flex items-start justify-between"><div><p>Formation professionnelle</p><h2>Performance mensuelle</h2></div><img class="h-16 w-auto" src="{{ asset('images/fp-logo.png') }}" alt="Formation Pro"></header>
                <div class="mt-[5vh] grid grid-cols-4 gap-4"><article class="kpi"><p class="kpi-label">Audience</p><p class="kpi-value">{{ number_format($stats['training_audience'],0,',',' ') }}</p></article><article class="kpi"><p class="kpi-label">Leads</p><p class="kpi-value">{{ number_format($stats['training_leads'],0,',',' ') }}</p></article><article class="kpi"><p class="kpi-label">Conversion</p><p class="kpi-value">{{ number_format($stats['training_conversion'],2,',',' ') }} %</p></article><article class="kpi"><p class="kpi-label">Demandes</p><p class="kpi-value">{{ $report->training['inbound_requests'] }}</p></article></div>
                <div class="mt-6 grid grid-cols-2 gap-5"><div class="bg-zinc-50 p-6"><p class="eyebrow">Offre à promouvoir</p><p class="mt-3 text-2xl font-bold">{{ $report->training['offer_to_promote'] }}</p><p class="mt-5 text-zinc-600">{{ $report->training['priority_audience'] }}</p></div><div class="bg-escm-600 p-6 text-white"><p class="text-xs font-bold uppercase">Décision COPIL</p><p class="mt-3 text-2xl font-bold">{{ $report->training['decision'] }}</p></div></div>
            </div></section>
        @elseif($number === '06')
            <section class="presentation-slide" data-slide><div class="presentation-content"><header class="presentation-title"><p>Informatique & outils</p><h2>État des projets</h2></header><div class="mt-[5vh] grid grid-cols-2 gap-4">@foreach(array_slice($report->tools,0,6) as $tool)<article class="bg-zinc-50 p-5"><div class="flex justify-between gap-4"><h3 class="text-xl font-bold">{{ $tool['name'] }}</h3><span class="tag tag-red">{{ $tool['status'] }}</span></div><p class="mt-3 text-sm text-zinc-600">{{ $tool['next_milestone'] }}</p><div class="progress-track mt-5"><div class="progress-bar" style="width: {{ $tool['progress'] }}%"></div></div></article>@endforeach</div></div></section>
        @elseif($number === '07')
            <section class="presentation-slide" data-slide><div class="presentation-content"><header class="presentation-title"><p>Événements</p><h2>Résultats et suites</h2></header><div class="mt-[6vh] grid grid-cols-2 gap-5">@foreach(array_slice($report->events,0,4) as $event)<article class="bg-zinc-50 p-6"><h3 class="text-2xl font-bold">{{ $event['name'] }}</h3><p class="mt-2 text-sm font-semibold text-escm-600">{{ $event['objective'] }}</p><p class="mt-5 text-lg leading-7">{{ $event['results'] }}</p><p class="mt-4 border-t border-zinc-200 pt-4 text-sm text-zinc-600">{{ $event['next_step'] }}</p></article>@endforeach</div></div></section>
        @else
            <section class="presentation-slide" data-slide><div class="presentation-content"><header class="presentation-title"><p>Projets & actions</p><h2>Priorités du prochain mois</h2></header><div class="mt-[5vh] grid grid-cols-2 gap-5"><div class="space-y-3">@foreach(array_slice($report->projects,0,4) as $project)<article class="bg-zinc-50 p-4"><div class="flex justify-between"><strong>{{ $project['name'] }}</strong><span>{{ $project['progress'] }} %</span></div><p class="mt-2 text-sm text-zinc-600">{{ $project['next_action'] }}</p></article>@endforeach</div><div class="space-y-3">@foreach(array_slice($report->action_plan,0,5) as $action)<article class="border-l-4 border-escm-500 bg-zinc-50 p-4"><div class="flex justify-between gap-4"><strong>{{ $action['action'] }}</strong><span class="tag tag-red">{{ $action['priority'] }}</span></div><p class="mt-2 text-sm text-zinc-600">{{ $action['owner'] }} · {{ $action['due_date'] }}</p></article>@endforeach</div></div></div></section>
        @endif
    @endforeach

    <section class="presentation-slide presentation-cover" data-slide style="background-image: url('{{ asset('images/copil-cover.png') }}')"><div class="presentation-content"><img class="mb-10 h-20 w-auto self-start" src="{{ asset('images/escm-logo.png') }}" alt="ESCM"><h1>Merci<span>Décisions & prochaines étapes</span></h1><p class="mt-8 max-w-4xl text-2xl font-semibold text-zinc-700">{{ $report->summary['copil_decision'] }}</p></div></section>

    <nav class="presentation-controls" aria-label="Contrôles de présentation">
        @auth<a class="btn btn-icon border-zinc-600 bg-zinc-800 text-white" href="{{ route('copil.index', ['period'=>$period->key]) }}" title="Quitter"><i data-lucide="x"></i></a>@endauth
        <button class="btn btn-icon border-zinc-600 bg-zinc-800 text-white" data-slide-prev title="Précédent"><i data-lucide="chevron-left"></i></button>
        <span class="min-w-16 text-center text-xs font-bold" data-slide-counter></span>
        <button class="btn btn-icon border-zinc-600 bg-zinc-800 text-white" data-slide-next title="Suivant"><i data-lucide="chevron-right"></i></button>
        <button class="btn btn-icon border-zinc-600 bg-zinc-800 text-white" data-fullscreen title="Plein écran"><i data-lucide="maximize"></i></button>
    </nav>
</main>
@endsection
