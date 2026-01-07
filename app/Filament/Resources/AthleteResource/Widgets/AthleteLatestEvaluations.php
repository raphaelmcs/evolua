<?php

namespace App\Filament\Resources\AthleteResource\Widgets;

use App\Models\Athlete;
use App\Models\Evaluation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class AthleteLatestEvaluations extends TableWidget
{
    public Athlete $record;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Ultimas avaliacoes';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('evaluated_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template'),
                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('Avaliador'),
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidade')
                    ->formatStateUsing(fn ($state) => $state === 'internal' ? 'Interna' : 'Compartilhavel')
                    ->colors([
                        'primary' => 'internal',
                        'success' => 'shareable',
                    ]),
            ])
            ->paginated(false);
    }

    protected function getTableQuery(): Builder
    {
        return Evaluation::query()
            ->where('athlete_id', $this->record->id)
            ->with(['template', 'evaluator'])
            ->latest('evaluated_at')
            ->limit(5);
    }
}
