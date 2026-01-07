<?php

namespace App\Filament\Resources\AthleteResource\Widgets;

use App\Models\Athlete;
use App\Models\Evaluation;
use App\Models\EvaluationTemplateItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AthleteDomainSummary extends BaseWidget
{
    public Athlete $record;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $evaluation = Evaluation::query()
            ->where('athlete_id', $this->record->id)
            ->with(['scores.templateItem'])
            ->latest('evaluated_at')
            ->first();

        $averages = $evaluation ? $evaluation->domainAverages() : [];

        return collect(EvaluationTemplateItem::DOMAINS)
            ->map(function (string $label, string $domain) use ($averages) {
                $value = $averages[$domain] ?? null;

                return Stat::make($label, $value !== null ? number_format($value, 2) : '-');
            })
            ->values()
            ->all();
    }
}
