<?php

use App\Models\Report;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/{report}/download', function (Report $report) {
        Gate::authorize('view', $report);

        if ($report->status !== 'ready' || ! $report->pdf_path) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($report->pdf_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($report->pdf_path);
    })->name('reports.download');
});

Route::get('/r/{token}', function (string $token) {
    $report = Report::query()
        ->where('public_token', $token)
        ->where('status', 'ready')
        ->with('evaluation.athlete')
        ->firstOrFail();

    if ($report->evaluation->visibility !== 'shareable') {
        abort(404);
    }

    if (! $report->pdf_path || ! Storage::disk('local')->exists($report->pdf_path)) {
        abort(404);
    }

    return Storage::disk('local')->download($report->pdf_path);
})->name('reports.public');
