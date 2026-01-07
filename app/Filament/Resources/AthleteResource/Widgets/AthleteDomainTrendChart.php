<?php

namespace App\Filament\Resources\AthleteResource\Widgets;

use App\Models\Athlete;
use App\Models\Evaluation;
use App\Models\EvaluationTemplateItem;
use Filament\Widgets\ChartWidget;

class AthleteDomainTrendChart extends ChartWidget
{
    public Athlete $record;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Evolucao por dominio';

    protected function getData(): array
    {
        $evaluations = Evaluation::query()
            ->where('athlete_id', $this->record->id)
            ->with(['scores.templateItem'])
            ->orderByDesc('evaluated_at')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $labels = $evaluations->map(function (Evaluation $evaluation) {
            return $evaluation->evaluated_at->format('d/m');
        });

        $palette = [
            'tecnico' => '#2563eb',
            'fisico' => '#16a34a',
            'tatico' => '#f59e0b',
            'mental' => '#ef4444',
        ];

        $datasets = [];

        foreach (EvaluationTemplateItem::DOMAINS as $domain => $label) {
            $datasets[] = [
                'label' => $label,
                'data' => $evaluations->map(function (Evaluation $evaluation) use ($domain) {
                    return $evaluation->domainAverages()[$domain] ?? null;
                }),
                'borderColor' => $palette[$domain],
                'backgroundColor' => $palette[$domain],
                'fill' => false,
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
