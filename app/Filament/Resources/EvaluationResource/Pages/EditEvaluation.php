<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Models\EvaluationScore;
use App\Models\EvaluationTemplate;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static string $view = 'filament.resources.evaluation-resource.pages.edit-evaluation';

    public function getHeading(): string
    {
        return '';
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'evolua-evaluations-form-bg',
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return array_merge($data, EvaluationResource::scoresFromEvaluation($this->record));
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $scores = $this->extractScores($data);

        $template = EvaluationTemplate::findOrFail($data['template_id']);
        $this->validateScores($scores, $template);

        return DB::transaction(function () use ($record, $data, $scores) {
            $record->update($data);
            $record->scores()->delete();

            foreach ($scores as $score) {
                EvaluationScore::create([
                    'evaluation_id' => $record->id,
                    'template_item_id' => $score['template_item_id'],
                    'score' => $score['score'],
                    'comment' => $score['comment'],
                ]);
            }

            return $record;
        });
    }

    protected function extractScores(array &$data): array
    {
        $groups = collect([
            'scores_tecnico',
            'scores_fisico',
            'scores_tatico',
            'scores_mental',
        ]);

        $scores = $groups
            ->flatMap(fn (string $key) => $data[$key] ?? [])
            ->map(function (array $item) {
                return [
                    'template_item_id' => $item['template_item_id'],
                    'score' => (float) $item['score'],
                    'comment' => $item['comment'] ?? null,
                ];
            })
            ->values()
            ->all();

        foreach ($groups as $key) {
            unset($data[$key]);
        }

        unset($data['scale_min'], $data['scale_max']);

        return $scores;
    }

    protected function validateScores(array $scores, EvaluationTemplate $template): void
    {
        $min = (float) $template->scale_min;
        $max = (float) $template->scale_max;

        foreach ($scores as $score) {
            if ($score['score'] < $min || $score['score'] > $max) {
                throw ValidationException::withMessages([
                    'scores_tecnico' => "As notas devem estar entre {$min} e {$max}.",
                ]);
            }
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
