<?php

namespace App\Filament\Pages;

use App\Models\Athlete;
use App\Models\Evaluation;
use App\Models\EvaluationTemplate;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected function getViewData(): array
    {
        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return [
                'organizationName' => null,
                'activeAthletes' => 0,
                'evaluationsThisMonth' => 0,
                'templatesCount' => 0,
                'lastEvaluationAt' => null,
            ];
        }

        $activeAthletes = Athlete::query()
            ->where('organization_id', $organizationId)
            ->where('active', true)
            ->count();

        $evaluationsThisMonth = Evaluation::query()
            ->where('organization_id', $organizationId)
            ->whereBetween('evaluated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $templatesCount = EvaluationTemplate::query()
            ->where(function ($query) use ($organizationId) {
                $query->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationId);
            })
            ->count();

        $lastEvaluationAt = Evaluation::query()
            ->where('organization_id', $organizationId)
            ->latest('evaluated_at')
            ->value('evaluated_at');

        return [
            'organizationName' => auth()->user()?->organization?->name,
            'activeAthletes' => $activeAthletes,
            'evaluationsThisMonth' => $evaluationsThisMonth,
            'templatesCount' => $templatesCount,
            'lastEvaluationAt' => $lastEvaluationAt,
        ];
    }
}
