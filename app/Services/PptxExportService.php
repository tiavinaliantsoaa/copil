<?php

namespace App\Services;

use App\Models\CommunicationReport;
use App\Models\Period;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use Illuminate\Support\Facades\Storage;

class PptxExportService
{
    private const RED = 'FFD9252A';
    private const DARK = 'FF17191C';
    private const GREY = 'FF6B7280';
    private const LIGHT = 'FFF4F5F6';
    private const WHITE = 'FFFFFFFF';

    private PhpPresentation $ppt;

    public function generate(Period $period, CommunicationReport $report, array $stats): string
    {
        $this->ppt = new PhpPresentation();
        $this->ppt->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
        $this->cover($this->ppt->getActiveSlide(), $period);
        $this->toc($this->ppt->createSlide());

        $slide = $this->frame('Synthèse du mois', 'Indicateurs consolidés et évolution M-1');
        $this->kpi($slide, 'Leads', number_format($stats['total_leads'], 0, ',', ' '), $this->delta($stats['total_leads_delta']), 38, 115);
        $this->kpi($slide, 'Portée', number_format($stats['reach'], 0, ',', ' '), $this->delta($stats['reach_delta']), 270, 115);
        $this->kpi($slide, 'Budget consommé', number_format($stats['budget_rate'], 1, ',', ' ').' %', 'Budget / dépenses', 502, 115);
        $this->kpi($slide, 'Complétude', $stats['completion'].' %', 'Données renseignées', 734, 115);
        $this->text($slide, 'Progression', $report->summary['progress'] ?? '', 38, 250, 438, 110, 17);
        $this->text($slide, 'Point de vigilance', $report->summary['decline'] ?? '', 500, 250, 438, 110, 17);
        $this->text($slide, 'Décision COPIL', $report->summary['copil_decision'] ?? '', 38, 390, 900, 105, 18, self::RED);

        $slide = $this->frame('Reporting réseaux sociaux', 'Leads par plateforme et résultats du mois');
        $x = 38;
        foreach ($report->channels as $channel) {
            $lead = $stats['leads'][$channel['id']] ?? 0;
            $this->kpi($slide, $channel['label'], number_format($lead, 0, ',', ' '), 'leads / conversions', $x, 115, 122);
            $x += 132;
        }
        $this->text($slide, 'Analyse', $report->summary['analysis'] ?? '', 38, 285, 430, 150, 17);
        $this->text($slide, 'Objectif final', $report->summary['final_objective'] ?? '', 500, 285, 438, 150, 17);

        $slide = $this->frame('Performance des contenus', 'Meilleur contenu et contenu à améliorer par plateforme');
        $rows = collect($report->channels)->take(5);
        $y = 108;
        foreach ($rows as $channel) {
            $content = $channel['content'] ?? [];
            $this->text($slide, $channel['label'].' · meilleur', ($content['best_title'] ?? '')."\n".($content['best_reason'] ?? ''), 38, $y, 430, 68, 13);
            $this->text($slide, $channel['label'].' · à améliorer', ($content['improvement_title'] ?? '')."\n".($content['improvement_reason'] ?? ''), 500, $y, 438, 68, 13);
            $y += 82;
        }

        $slide = $this->frame('Graphisme & visuels', 'Production du mois et aperçu des créations');
        $graphics = $report->graphics;
        $this->kpi($slide, 'Visuels produits', $graphics['visuals_produced'] ?? 0, 'créations', 38, 115);
        $this->kpi($slide, 'Campagnes', $graphics['campaigns_created'] ?? 0, 'déclinaisons', 270, 115);
        $this->kpi($slide, 'Délai moyen', ($graphics['average_delivery_days'] ?? 0).' j', 'production', 502, 115);
        $this->kpi($slide, 'Révisions majeures', $graphics['major_revisions'] ?? 0, 'retours', 734, 115);
        $this->text($slide, 'Analyse', $graphics['note'] ?? '', 38, 270, 900, 110, 18);
        $this->firstImage($slide, $report, 'graphics', 38, 400, 290, 120);

        $slide = $this->frame('Formation professionnelle', 'Performance calculée automatiquement');
        $this->kpi($slide, 'Audience', number_format($stats['training_audience'], 0, ',', ' '), $this->delta($stats['training_audience_delta']), 38, 115);
        $this->kpi($slide, 'Leads', number_format($stats['training_leads'], 0, ',', ' '), $this->delta($stats['training_leads_delta']), 270, 115);
        $this->kpi($slide, 'Conversion', number_format($stats['training_conversion'], 2, ',', ' ').' %', 'leads / audience', 502, 115);
        $this->kpi($slide, 'Demandes', $report->training['inbound_requests'] ?? 0, 'entrantes', 734, 115);
        $this->text($slide, 'Offre à promouvoir', $report->training['offer_to_promote'] ?? '', 38, 270, 430, 120, 17);
        $this->text($slide, 'Décision', $report->training['decision'] ?? '', 500, 270, 438, 120, 17, self::RED);

        $this->workSlide('Informatique & outils', $report->tools, 'next_milestone');
        $this->workSlide('Événements', $report->events, 'next_step');
        $this->workSlide('Projets prioritaires', $report->projects, 'next_action');
        $this->workSlide('Actions & décisions', $report->action_plan, 'expected_kpi');

        $slide = $this->frame('Merci', 'ESCM Business School · COPIL Communication');
        $this->text($slide, 'Décision finale', $report->summary['copil_decision'] ?? '', 120, 210, 720, 150, 25, self::RED);

        $path = tempnam(sys_get_temp_dir(), 'escm-copil-').'.pptx';
        IOFactory::createWriter($this->ppt, 'PowerPoint2007')->save($path);
        return $path;
    }

    private function cover(Slide $slide, Period $period): void
    {
        $cover = public_path('images/copil-cover.png');
        if (is_file($cover)) {
            $slide->createDrawingShape()->setPath($cover)->setWidth(960)->setHeight(540)->setOffsetX(0)->setOffsetY(0);
        }
        $this->box($slide, 0, 0, 960, 540, '66FFFFFF');
        $this->text($slide, 'ESCM COPIL', 'COMMUNICATION', 60, 125, 480, 80, 34, self::RED, true);
        $this->plain($slide, $period->label, 60, 225, 450, 45, 25, self::DARK, true);
        $this->plain($slide, 'Résultats, contenus, projets et décisions', 60, 280, 480, 35, 16, self::GREY);
        $this->logo($slide, 'images/escm-logo.png', 60, 42, 170, 75);
    }

    private function toc(Slide $slide): void
    {
        $this->header($slide, 'Sommaire', 'Le reporting mensuel en un seul parcours');
        $items = ['01  Synthèse', '02  Reporting réseaux sociaux', '03  Graphisme & visuels', '04  Formation professionnelle', '05  Informatique & outils', '06  Événements', '07  Projets', '08  Actions & décisions'];
        foreach ($items as $index => $item) {
            $x = $index % 2 === 0 ? 60 : 510;
            $y = 120 + intdiv($index, 2) * 85;
            $this->plain($slide, $item, $x, $y, 390, 48, 18, self::DARK, true);
        }
    }

    private function frame(string $title, string $subtitle): Slide
    {
        $slide = $this->ppt->createSlide();
        $this->header($slide, $title, $subtitle);
        return $slide;
    }

    private function header(Slide $slide, string $title, string $subtitle): void
    {
        $this->box($slide, 0, 0, 960, 12, self::RED);
        $this->plain($slide, strtoupper($subtitle), 38, 30, 760, 20, 9, self::RED, true);
        $this->plain($slide, $title, 38, 55, 820, 48, 28, self::DARK, true);
        $this->logo($slide, 'images/escm-logo.png', 830, 25, 90, 48);
    }

    private function kpi(Slide $slide, string $label, string|int|float $value, string $note, int $x, int $y, int $width = 204): void
    {
        $this->box($slide, $x, $y, $width, 118, self::LIGHT);
        $this->plain($slide, strtoupper($label), $x + 14, $y + 14, $width - 28, 18, 9, self::GREY, true);
        $this->plain($slide, (string) $value, $x + 14, $y + 40, $width - 28, 36, 25, self::DARK, true);
        $this->plain($slide, $note, $x + 14, $y + 84, $width - 28, 20, 10, self::RED);
    }

    private function text(Slide $slide, string $label, string $body, int $x, int $y, int $width, int $height, int $size, string $accent = self::DARK, bool $center = false): void
    {
        $this->box($slide, $x, $y, $width, $height, self::LIGHT);
        $this->plain($slide, strtoupper($label), $x + 16, $y + 12, $width - 32, 18, 9, $accent, true);
        $this->plain($slide, $body ?: 'À compléter', $x + 16, $y + 38, $width - 32, $height - 46, $size, self::DARK, false, $center);
    }

    private function workSlide(string $title, array $items, string $detailKey): void
    {
        $slide = $this->frame($title, 'État d’avancement, points de blocage et prochaines étapes');
        $y = 112;
        foreach (array_slice($items, 0, 5) as $item) {
            $name = $item['name'] ?? $item['action'] ?? 'Élément';
            $status = $item['status'] ?? $item['priority'] ?? '';
            $detail = $item[$detailKey] ?? '';
            $this->box($slide, 38, $y, 900, 70, self::LIGHT);
            $this->plain($slide, $name, 55, $y + 12, 500, 24, 16, self::DARK, true);
            $this->plain($slide, $detail, 55, $y + 39, 690, 20, 11, self::GREY);
            $this->plain($slide, $status, 770, $y + 22, 145, 22, 11, self::RED, true, true);
            $y += 82;
        }
        if (!$items) $this->plain($slide, 'Aucun élément renseigné pour ce mois.', 55, 145, 700, 40, 18, self::GREY);
    }

    private function firstImage(Slide $slide, CommunicationReport $report, string $category, int $x, int $y, int $width, int $height): void
    {
        $attachment = $report->attachments->first(fn ($item) => $item->category === $category && $item->isImage());
        if (!$attachment) return;
        $path = Storage::disk('local')->path($attachment->path);
        if (is_file($path)) $slide->createDrawingShape()->setPath($path)->setWidth($width)->setHeight($height)->setOffsetX($x)->setOffsetY($y);
    }

    private function logo(Slide $slide, string $relative, int $x, int $y, int $width, int $height): void
    {
        $path = public_path($relative);
        if (is_file($path)) $slide->createDrawingShape()->setPath($path)->setWidth($width)->setHeight($height)->setOffsetX($x)->setOffsetY($y);
    }

    private function box(Slide $slide, int $x, int $y, int $width, int $height, string $color): void
    {
        $shape = $slide->createRichTextShape()->setWidth($width)->setHeight($height)->setOffsetX($x)->setOffsetY($y);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
    }

    private function plain(Slide $slide, string $text, int $x, int $y, int $width, int $height, int $size, string $color, bool $bold = false, bool $center = false): RichText
    {
        $shape = $slide->createRichTextShape()->setWidth($width)->setHeight($height)->setOffsetX($x)->setOffsetY($y);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal($center ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT);
        $run = $shape->createTextRun($text);
        $run->getFont()->setName('Aptos')->setSize($size)->setBold($bold)->setColor(new Color($color));
        return $shape;
    }

    private function delta(array $delta): string
    {
        $prefix = $delta['value'] > 0 ? '+' : '';
        return $prefix.number_format($delta['value'], 0, ',', ' ').' · '.$prefix.number_format($delta['percent'], 1, ',', ' ').' % vs M-1';
    }
}
