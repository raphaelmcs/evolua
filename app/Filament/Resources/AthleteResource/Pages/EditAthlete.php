<?php

namespace App\Filament\Resources\AthleteResource\Pages;

use App\Filament\Resources\AthleteResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditAthlete extends EditRecord
{
    protected static string $resource = AthleteResource::class;

    protected static string $view = 'filament.resources.athlete-resource.pages.edit-athlete';

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

    protected function beforeSave(): void
    {
        $record = $this->record;
        $nextActive = (bool) ($this->data['active'] ?? $record->active);

        if ($nextActive && ! $record->active && ! Gate::allows('activate', $record)) {
            Notification::make()
                ->title('Limite de atletas atingido')
                ->body('Seu plano atual nao permite ativar mais atletas.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
