<?php

namespace App\Jobs;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateEvaluationReportJob implements ShouldQueue
{
    use Queueable;

    public string $reportId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report = Report::query()->with([
            'evaluation.athlete',
            'evaluation.template.items',
            'evaluation.scores.templateItem',
            'evaluation.evaluator',
            'organization',
        ])->find($this->reportId);

        if (! $report) {
            return;
        }

        $report->update(['status' => 'processing']);

        try {
            $evaluation = $report->evaluation;

            if (! $evaluation) {
                $report->status = 'failed';
                $report->save();
                return;
            }

            $domainAverages = $evaluation->domainAverages();

            $pdf = Pdf::loadView('reports.evaluation', [
                'report' => $report,
                'evaluation' => $evaluation,
                'athlete' => $evaluation->athlete,
                'template' => $evaluation->template,
                'scores' => $evaluation->scores,
                'domainAverages' => $domainAverages,
            ]);

            $path = "reports/{$report->organization_id}/{$report->id}.pdf";
            Storage::disk('local')->put($path, $pdf->output());

            if ($evaluation->visibility === 'shareable' && ! $report->public_token) {
                $report->public_token = Str::random(32);
            }

            $report->status = 'ready';
            $report->pdf_path = $path;
            $report->save();
        } catch (Throwable $exception) {
            $report->status = 'failed';
            $report->save();

            throw $exception;
        }
    }
}
