<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 0; size: A4 landscape; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #17191c; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    .page { position: relative; width: 100%; min-height: 100%; padding: 28px 34px; page-break-after: always; overflow: hidden; }
    .page:last-child { page-break-after: auto; }
    .redline { height: 7px; background: #d9252a; position: absolute; top: 0; left: 0; right: 0; }
    h1,h2,h3,p { margin: 0; }
    h1 { font-size: 38px; line-height: 1.12; }
    h2 { font-size: 24px; margin: 7px 0 4px; }
    h3 { font-size: 13px; margin-bottom: 5px; }
    .eyebrow { color: #d9252a; font-size: 8px; text-transform: uppercase; font-weight: bold; }
    .muted { color: #687079; }
    .logo { width: 125px; height: 58px; object-fit: contain; }
    .fp-logo { width: 130px; height: 50px; object-fit: contain; float: right; }
    .header { border-top: 6px solid #d9252a; padding-top: 10px; margin-bottom: 18px; }
    .cover { background-image: url('{{ public_path('images/copil-cover.png') }}'); background-size: cover; background-position: center; padding: 58px; }
    .cover-box { width: 56%; margin-top: 74px; padding: 32px; background: rgba(255,255,255,.86); }
    .cover h1 span { display: block; color: #d9252a; }
    .kpis { width: 100%; border-spacing: 9px; margin: 0 -9px 18px; }
    .kpis td { width: 25%; background: #f3f4f5; padding: 14px; vertical-align: top; }
    .kpis strong { display: block; font-size: 24px; margin: 7px 0 4px; }
    .delta { color: #147a50; font-weight: bold; }
    .grid { width: 100%; border-spacing: 10px; margin: 0 -10px; }
    .grid td { width: 50%; vertical-align: top; }
    .box { background: #f5f6f7; padding: 13px; margin-bottom: 10px; }
    .decision { border-left: 5px solid #d9252a; }
    table.data { width: 100%; border-collapse: collapse; }
    .data th { background: #f3f4f5; color: #687079; font-size: 8px; text-transform: uppercase; text-align: left; padding: 8px; }
    .data td { border-bottom: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
    .platform { border-top: 4px solid #d9252a; }
    .thumbs img { width: 22%; height: 92px; object-fit: cover; margin: 0 2% 8px 0; }
    .footer { position: absolute; bottom: 15px; left: 34px; right: 34px; color: #8a9096; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
</style>
</head>
<body>
<section class="page cover">
    <img class="logo" src="{{ public_path('images/escm-logo.png') }}" alt="ESCM">
    <div class="cover-box"><p class="eyebrow">{{ $period->label }}</p><h1>ESCM COPIL <span>Communication</span></h1><p class="muted" style="font-size:15px;margin-top:14px">Résultats, contenus, projets et décisions</p></div>
</section>

<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Synthèse</p><h2>{{ $period->label }} en un regard</h2></header>
    <table class="kpis"><tr><td><span class="eyebrow">Leads</span><strong>{{ number_format($stats['total_leads'],0,',',' ') }}</strong><span class="delta">{{ $stats['total_leads_delta']['percent']>0?'+':'' }}{{ number_format($stats['total_leads_delta']['percent'],1,',',' ') }} % M-1</span></td><td><span class="eyebrow">Portée</span><strong>{{ number_format($stats['reach'],0,',',' ') }}</strong><span class="delta">{{ $stats['reach_delta']['percent']>0?'+':'' }}{{ number_format($stats['reach_delta']['percent'],1,',',' ') }} % M-1</span></td><td><span class="eyebrow">Budget</span><strong>{{ number_format($stats['budget_rate'],1,',',' ') }} %</strong><span class="muted">consommé</span></td><td><span class="eyebrow">Complétude</span><strong>{{ $stats['completion'] }} %</strong><span class="muted">du reporting</span></td></tr></table>
    <table class="grid"><tr><td><div class="box"><h3>Ce qui progresse</h3><p>{{ $report->summary['progress'] }}</p></div><div class="box"><h3>Analyse</h3><p>{{ $report->summary['analysis'] }}</p></div></td><td><div class="box"><h3>Point de vigilance</h3><p>{{ $report->summary['decline'] }}</p></div><div class="box decision"><h3>Décision COPIL</h3><p>{{ $report->summary['copil_decision'] }}</p></div></td></tr></table>
    <div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>

<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Reporting réseaux sociaux</p><h2>Leads et performance par plateforme</h2></header>
    <table class="data"><thead><tr><th>Plateforme</th><th>Leads</th><th>Performance</th><th>Analyse</th><th>Prochaine action</th></tr></thead><tbody>@foreach($report->channels as $channel)<tr><td><strong>{{ $channel['label'] }}</strong><br><span class="muted">{{ $channel['objective'] }}</span></td><td style="font-size:17px;font-weight:bold">{{ number_format($stats['leads'][$channel['id']]??0,0,',',' ') }}</td><td>{{ $channel['performance'] }}</td><td>{{ $channel['analysis'] }}</td><td>{{ $channel['next_action'] }}</td></tr>@endforeach</tbody></table>
    <div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>

@foreach(array_chunk($report->channels, 3) as $group)
<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Performance contenus</p><h2>{{ collect($group)->pluck('label')->join(' · ') }}</h2></header>
    <table class="data"><thead><tr><th>Plateforme</th><th>Meilleur contenu</th><th>Pourquoi</th><th>Contenu à améliorer</th><th>Amélioration attendue</th></tr></thead><tbody>@foreach($group as $channel)<tr><td><strong>{{ $channel['label'] }}</strong></td><td>{{ $channel['content']['best_title'] }}</td><td>{{ $channel['content']['best_reason'] }}</td><td>{{ $channel['content']['improvement_title'] }}</td><td>{{ $channel['content']['improvement_reason'] }}</td></tr>@endforeach</tbody></table>
    <div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>
@endforeach

<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Graphisme & visuels</p><h2>Production créative</h2></header>
    <table class="kpis"><tr>@foreach(['visuals_produced'=>'Visuels produits','campaigns_created'=>'Campagnes','average_delivery_days'=>'Délai moyen','major_revisions'=>'Révisions'] as $key=>$label)<td><span class="eyebrow">{{ $label }}</span><strong>{{ $report->graphics[$key] }}</strong></td>@endforeach</tr></table>
    <div class="box"><h3>Analyse</h3><p>{{ $report->graphics['note'] }}</p></div><div class="thumbs">@foreach($attachments->get('graphics',collect())->filter(fn($file)=>$file->isImage())->take(8) as $file)<img src="{{ \Illuminate\Support\Facades\Storage::disk('local')->path($file->path) }}" alt="">@endforeach</div>
    <div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>

<section class="page"><div class="redline"></div><img class="fp-logo" src="{{ public_path('images/fp-logo.png') }}" alt="Formation Pro"><header class="header"><p class="eyebrow">Formation professionnelle</p><h2>Performance mensuelle</h2></header>
    <table class="kpis"><tr><td><span class="eyebrow">Audience</span><strong>{{ number_format($stats['training_audience'],0,',',' ') }}</strong></td><td><span class="eyebrow">Leads</span><strong>{{ number_format($stats['training_leads'],0,',',' ') }}</strong></td><td><span class="eyebrow">Conversion</span><strong>{{ number_format($stats['training_conversion'],2,',',' ') }} %</strong></td><td><span class="eyebrow">Demandes</span><strong>{{ $report->training['inbound_requests'] }}</strong></td></tr></table>
    <table class="data"><thead><tr><th>Canal</th><th>Objectif</th><th>Audience</th><th>Leads</th><th>Action</th></tr></thead><tbody>@foreach($report->training['channels'] as $channel)<tr><td><strong>{{ $channel['label'] }}</strong></td><td>{{ $channel['objective'] }}</td><td>{{ number_format($channel['value'],0,',',' ') }}</td><td>{{ number_format($channel['leads'],0,',',' ') }}</td><td>{{ $channel['action'] }}</td></tr>@endforeach</tbody></table>
    <div class="box decision" style="margin-top:12px"><h3>Décision</h3><p>{{ $report->training['decision'] }}</p></div><div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>

<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Informatique, événements et projets</p><h2>État d’avancement</h2></header>
    <table class="grid"><tr><td><h3>Informatique & outils</h3>@foreach($report->tools as $item)<div class="box"><strong>{{ $item['name'] }} · {{ $item['progress'] }} %</strong><p class="muted">{{ $item['next_milestone'] }}</p></div>@endforeach</td><td><h3>Projets prioritaires</h3>@foreach($report->projects as $item)<div class="box"><strong>{{ $item['name'] }} · {{ $item['status'] }}</strong><p class="muted">{{ $item['next_action'] }}</p></div>@endforeach</td></tr></table>
    <div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>

<section class="page"><div class="redline"></div><header class="header"><p class="eyebrow">Actions & décisions</p><h2>Priorités du prochain mois</h2></header>
    <table class="data"><thead><tr><th>Priorité</th><th>Action</th><th>Propriétaire</th><th>Échéance</th><th>KPI attendu</th><th>Statut</th></tr></thead><tbody>@foreach($report->action_plan as $action)<tr><td><strong>{{ $action['priority'] }}</strong></td><td>{{ $action['action'] }}</td><td>{{ $action['owner'] }}</td><td>{{ $action['due_date'] }}</td><td>{{ $action['expected_kpi'] }}</td><td>{{ $action['status'] }}</td></tr>@endforeach</tbody></table>
    <div class="box decision" style="margin-top:18px"><h3>Décision finale</h3><p>{{ $report->summary['copil_decision'] }}</p></div><div class="footer">ESCM COPIL Communication · {{ $period->label }}</div>
</section>
</body>
</html>
