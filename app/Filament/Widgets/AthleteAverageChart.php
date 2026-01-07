<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AthleteAverageChart extends ChartWidget
{
    protected static ?string $heading = 'Media por atleta';

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return ['datasets' => [], 'labels' => []];
        }

        $rows = DB::table('evaluation_scores')
            ->join('evaluations', 'evaluation_scores.evaluation_id', '=', 'evaluations.id')
            ->join('athletes', 'evaluations.athlete_id', '=', 'athletes.id')
            ->join('evaluation_template_items', 'evaluation_scores.template_item_id', '=', 'evaluation_template_items.id')
            ->where('evaluations.organization_id', $organizationId)
            ->where('athletes.active', true)
            ->selectRaw(
                'athletes.id, athletes.name,
                SUM(evaluation_scores.score * evaluation_template_items.weight) / NULLIF(SUM(evaluation_template_items.weight), 0) as avg_score'
            )
            ->groupBy('athletes.id', 'athletes.name')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        $labels = $rows->pluck('name')->all();
        $data = $rows->pluck('avg_score')->map(fn ($value) => round((float) $value, 2))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Media',
                    'data' => $data,
                    'backgroundColor' => '#16a34a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
