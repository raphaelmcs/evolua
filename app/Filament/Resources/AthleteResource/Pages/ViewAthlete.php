<?php

namespace App\Filament\Resources\AthleteResource\Pages;

use App\Filament\Resources\AthleteResource;
use App\Filament\Resources\AthleteResource\Widgets\AthleteDomainSummary;
use App\Filament\Resources\AthleteResource\Widgets\AthleteDomainTrendChart;
use App\Filament\Resources\AthleteResource\Widgets\AthleteLatestEvaluations;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAthlete extends ViewRecord
{
    protected static string $resource = AthleteResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AthleteDomainSummary::make(['record' => $this->record]),
            AthleteDomainTrendChart::make(['record' => $this->record]),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AthleteLatestEvaluations::make(['record' => $this->record]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
