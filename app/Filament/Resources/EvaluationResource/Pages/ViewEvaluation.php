<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Jobs\GenerateEvaluationReportJob;
use App\Models\Report;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_report')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(function () {
                    return ! $this->record->report || $this->record->report->status !== 'ready';
                })
                ->action(function (): void {
                    $report = Report::firstOrCreate(
                        [
                            'evaluation_id' => $this->record->id,
                            'organization_id' => $this->record->organization_id,
                        ],
                        [
                            'status' => 'pending',
                        ]
                    );

                    GenerateEvaluationReportJob::dispatch($report->id);

                    Notification::make()
                        ->title('Relatorio em processamento.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('download_report')
                ->label('Baixar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(function () {
                    return $this->record->report && $this->record->report->status === 'ready';
                })
                ->url(fn () => route('reports.download', $this->record->report))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
