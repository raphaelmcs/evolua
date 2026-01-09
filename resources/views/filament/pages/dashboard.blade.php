<x-filament-panels::page class="evolua-dashboard">
    <div class="evolua-dashboard-hero">
        <div>
            <p class="evolua-dashboard-kicker">Painel</p>
            <h1 class="evolua-dashboard-title">Ola, {{ auth()->user()?->name }}.</h1>
            <p class="evolua-dashboard-subtitle">
                Visao geral da organizacao {{ $organizationName ?? 'EVOLUA' }}.
            </p>
        </div>
        <div class="evolua-dashboard-highlight">
            <span>Ultima avaliacao</span>
            <strong>
                {{ $lastEvaluationAt ? \Illuminate\Support\Carbon::parse($lastEvaluationAt)->format('d/m/Y') : 'Sem registros' }}
            </strong>
        </div>
    </div>

    <div class="evolua-dashboard-metrics">
        <div class="evolua-metric-card">
            <span class="evolua-metric-label">Atletas ativos</span>
            <span class="evolua-metric-value">{{ $activeAthletes }}</span>
            <span class="evolua-metric-hint">Total em atividade</span>
        </div>
        <div class="evolua-metric-card">
            <span class="evolua-metric-label">Avaliacoes no mes</span>
            <span class="evolua-metric-value">{{ $evaluationsThisMonth }}</span>
            <span class="evolua-metric-hint">Periodo atual</span>
        </div>
        <div class="evolua-metric-card">
            <span class="evolua-metric-label">Templates disponiveis</span>
            <span class="evolua-metric-value">{{ $templatesCount }}</span>
            <span class="evolua-metric-hint">Globais + organizacao</span>
        </div>
    </div>

    <div class="evolua-dashboard-grid">
        <div class="evolua-dashboard-chart">
            @livewire(\App\Filament\Widgets\AthleteCountChart::class)
        </div>
        <div class="evolua-dashboard-chart">
            @livewire(\App\Filament\Widgets\PositionAverageChart::class)
        </div>
        <div class="evolua-dashboard-chart">
            @livewire(\App\Filament\Widgets\AthleteAverageChart::class)
        </div>
    </div>
</x-filament-panels::page>
