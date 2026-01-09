<?php

namespace App\Filament\Resources\AthleteResource\Pages;

use App\Filament\Resources\AthleteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAthletes extends ListRecords
{
    protected static string $resource = AthleteResource::class;

    protected static string $view = 'filament.resources.athlete-resource.pages.list-athletes';

    public function getHeading(): string
    {
        return '';
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'evolua-athletes-bg',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo atleta'),
        ];
    }
}
