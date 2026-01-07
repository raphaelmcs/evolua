<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PositionAverageChart extends ChartWidget
{
    protected static ?string $heading = 'Media por posicao';

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
                "COALESCE(NULLIF(athletes.position, ''), 'Sem posicao') as position,
                SUM(evaluation_scores.score * evaluation_template_items.weight) / NULLIF(SUM(evaluation_template_items.weight), 0) as avg_score"
            )
            ->groupBy('position')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        $labels = $rows->pluck('position')->all();
        $data = $rows->pluck('avg_score')->map(fn ($value) => round((float) $value, 2))->all();
        $palette = [
            '#0ea5e9',
            '#22c55e',
            '#f97316',
            '#a855f7',
            '#14b8a6',
            '#ef4444',
            '#eab308',
            '#6366f1',
            '#ec4899',
            '#10b981',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Media',
                    'data' => $data,
                    'backgroundColor' => array_slice($palette, 0, max(count($data), 1)),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
