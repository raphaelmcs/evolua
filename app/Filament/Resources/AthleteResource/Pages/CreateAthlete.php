<?php

namespace App\Filament\Resources\AthleteResource\Pages;

use App\Filament\Resources\AthleteResource;
use App\Models\Athlete;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Gate;

class CreateAthlete extends CreateRecord
{
    protected static string $resource = AthleteResource::class;

    protected static string $view = 'filament.resources.athlete-resource.pages.create-athlete';

    public function getHeading(): string
    {
        return '';
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'evolua-athletes-form-bg',
        ];
    }

    protected function beforeCreate(): void
    {
        if (! Gate::allows('create', Athlete::class)) {
            Notification::make()
                ->title('Limite de atletas atingido')
                ->body('Seu plano atual nao permite cadastrar mais atletas ativos.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
