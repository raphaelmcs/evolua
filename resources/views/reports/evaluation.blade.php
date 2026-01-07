<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatorio de Avaliacao</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1, h2, h3 { margin: 0 0 6px 0; }
        .muted { color: #6b7280; }
        .section { margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; background: #e5e7eb; font-size: 11px; }
    </style>
</head>
<body>
    <div class="section">
        <h1>Relatorio de Avaliacao</h1>
        <div class="muted">{{ $report->organization?->name ?? 'Organizacao' }}</div>
        <div class="muted">{{ $evaluation->evaluated_at->format('d/m/Y H:i') }}</div>
    </div>

    <div class="section">
        <h2>Atleta</h2>
        <table>
            <tr>
                <th>Nome</th>
                <td>{{ $athlete->name }}</td>
                <th>Posicao</th>
                <td>{{ $athlete->position ?? '-' }}</td>
            </tr>
            <tr>
                <th>Categoria</th>
                <td>{{ $athlete->category ?? '-' }}</td>
                <th>Nascimento</th>
                <td>{{ $athlete->birthdate ? $athlete->birthdate->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Avaliador</th>
                <td>{{ $evaluation->evaluator?->name ?? '-' }}</td>
                <th>Template</th>
                <td>{{ $template->name }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Medias por dominio</h2>
        <table>
            <tr>
                @foreach (\App\Models\EvaluationTemplateItem::DOMAINS as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach (\App\Models\EvaluationTemplateItem::DOMAINS as $domain => $label)
                    <td>
                        {{ isset($domainAverages[$domain]) ? number_format($domainAverages[$domain], 2) : '-' }}
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Criterios e notas</h2>
        @php
            $grouped = $scores->groupBy(fn ($score) => $score->templateItem->domain);
        @endphp
        @foreach (\App\Models\EvaluationTemplateItem::DOMAINS as $domain => $label)
            <h3>{{ $label }}</h3>
            <table>
                <tr>
                    <th>Criterio</th>
                    <th>Nota</th>
                    <th>Observacao</th>
                </tr>
                @foreach ($grouped->get($domain, collect()) as $score)
                    <tr>
                        <td>{{ $score->templateItem->label }}</td>
                        <td>{{ $score->score }}</td>
                        <td>{{ $score->comment ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
            <br>
        @endforeach
    </div>

    <div class="section">
        <h2>Observacoes gerais</h2>
        <div>{{ $evaluation->notes ?? '-' }}</div>
    </div>
</body>
</html>
