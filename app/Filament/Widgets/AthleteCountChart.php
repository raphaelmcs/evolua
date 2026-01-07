<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AthleteCountChart extends ChartWidget
{
    protected static ?string $heading = 'Cadastros de atletas';

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return ['datasets' => [], 'labels' => []];
        }

        $months = collect(range(0, 5))
            ->map(fn (int $offset) => now()->subMonths(5 - $offset))
            ->values();

        $rows = DB::table('athletes')
            ->where('organization_id', $organizationId)
            ->where('created_at', '>=', $months->first()->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $labels = $months->map(fn (Carbon $month) => $month->format('m/Y'))->all();
        $data = $months->map(function (Carbon $month) use ($rows) {
            return (int) ($rows[$month->format('Y-m')] ?? 0);
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Atletas',
                    'data' => $data,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.2)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
