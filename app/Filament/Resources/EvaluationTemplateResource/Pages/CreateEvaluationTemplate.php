<?php

namespace App\Filament\Resources\EvaluationTemplateResource\Pages;

use App\Filament\Resources\EvaluationTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvaluationTemplate extends CreateRecord
{
    protected static string $resource = EvaluationTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->organization_id;

        return $data;
    }
}
