<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\EvaluationTemplate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static string $view = 'filament.resources.evaluation-resource.pages.create-evaluation';

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

    protected function handleRecordCreation(array $data): Model
    {
        $scores = $this->extractScores($data);

        $template = EvaluationTemplate::findOrFail($data['template_id']);
        $this->validateScores($scores, $template);

        $data['evaluator_user_id'] = auth()->id();

        return DB::transaction(function () use ($data, $scores) {
            $evaluation = Evaluation::create($data);

            foreach ($scores as $score) {
                EvaluationScore::create([
                    'evaluation_id' => $evaluation->id,
                    'template_item_id' => $score['template_item_id'],
                    'score' => $score['score'],
                    'comment' => $score['comment'],
                ]);
            }

            return $evaluation;
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
}
