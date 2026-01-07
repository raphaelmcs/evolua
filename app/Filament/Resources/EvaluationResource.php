<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationTemplateItem;
use App\Models\Report;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\GenerateEvaluationReportJob;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Avaliacoes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('scale_min'),
                Forms\Components\Hidden::make('scale_max'),
                Forms\Components\Section::make('Avaliacao')
                    ->schema([
                        Forms\Components\Select::make('athlete_id')
                            ->label('Atleta')
                            ->relationship('athlete', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('template_id')
                            ->label('Template')
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => static::defaultTemplateId())
                            ->live()
                            ->afterStateHydrated(function (Set $set, Get $get, $state): void {
                                if ($get('scores_tecnico')) {
                                    return;
                                }

                                static::applyScoresFromTemplate($set, $state);
                            })
                            ->afterStateUpdated(function (Set $set, $state): void {
                                static::applyScoresFromTemplate($set, $state);
                            })
                            ->disabled(fn (?Evaluation $record) => filled($record)),
                        Forms\Components\DateTimePicker::make('evaluated_at')
                            ->label('Data da avaliacao')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('visibility')
                            ->label('Visibilidade')
                            ->options([
                                'internal' => 'Interna',
                                'shareable' => 'Compartilhavel',
                            ])
                            ->default('internal')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observacoes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Group::make()
                    ->schema(static::scoreSections()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('athlete.name')
                    ->label('Atleta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable(),
                Tables\Columns\TextColumn::make('evaluated_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('visibility')
                    ->label('Visibilidade')
                    ->formatStateUsing(fn ($state) => $state === 'internal' ? 'Interna' : 'Compartilhavel')
                    ->colors([
                        'primary' => 'internal',
                        'success' => 'shareable',
                    ]),
                Tables\Columns\BadgeColumn::make('report.status')
                    ->label('Relatorio')
                    ->formatStateUsing(fn ($state) => $state ?? 'nao gerado')
                    ->colors([
                        'secondary' => ['nao gerado', 'pending'],
                        'warning' => 'processing',
                        'success' => 'ready',
                        'danger' => 'failed',
                    ]),
            ])
            ->filters([
                //
            ])
            ->defaultSort('evaluated_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('generate_report')
                    ->label('Gerar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(function (Evaluation $record) {
                        return ! $record->report || $record->report->status !== 'ready';
                    })
                    ->action(function (Evaluation $record): void {
                        $report = Report::firstOrCreate(
                            [
                                'evaluation_id' => $record->id,
                                'organization_id' => $record->organization_id,
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
                Tables\Actions\Action::make('download_report')
                    ->label('Baixar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function (Evaluation $record) {
                        return $record->report && $record->report->status === 'ready';
                    })
                    ->url(function (Evaluation $record) {
                        return route('reports.download', $record->report);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['athlete', 'template', 'report']);
    }

    protected static function defaultTemplateId(): ?string
    {
        $organizationId = auth()->user()?->organization_id;

        if (! $organizationId) {
            return null;
        }

        return EvaluationTemplate::defaultForOrganization($organizationId)->value('id');
    }

    protected static function applyScoresFromTemplate(Set $set, ?string $templateId): void
    {
        $scores = static::scoresForTemplate($templateId);

        $set('scores_tecnico', $scores['scores_tecnico']);
        $set('scores_fisico', $scores['scores_fisico']);
        $set('scores_tatico', $scores['scores_tatico']);
        $set('scores_mental', $scores['scores_mental']);
        $set('scale_min', $scores['scale_min']);
        $set('scale_max', $scores['scale_max']);
    }

    protected static function scoresForTemplate(?string $templateId): array
    {
        $empty = [
            'scores_tecnico' => [],
            'scores_fisico' => [],
            'scores_tatico' => [],
            'scores_mental' => [],
            'scale_min' => null,
            'scale_max' => null,
        ];

        if (! $templateId) {
            return $empty;
        }

        $template = EvaluationTemplate::query()
            ->with('items')
            ->find($templateId);

        if (! $template) {
            return $empty;
        }

        $items = $template->items->sortBy('sort_order');

        $payload = collect(EvaluationTemplateItem::DOMAINS)
            ->mapWithKeys(function (string $label, string $domain) use ($items) {
                $scores = $items
                    ->where('domain', $domain)
                    ->values()
                    ->map(function (EvaluationTemplateItem $item) {
                        return [
                            'template_item_id' => $item->id,
                            'label' => $item->label,
                            'score' => null,
                            'comment' => null,
                        ];
                    })
                    ->all();

                return ["scores_{$domain}" => $scores];
            })
            ->all();

        return array_merge($payload, [
            'scale_min' => (float) $template->scale_min,
            'scale_max' => (float) $template->scale_max,
        ]);
    }

    public static function scoresFromEvaluation(Evaluation $evaluation): array
    {
        $evaluation->loadMissing(['scores.templateItem', 'template']);

        $scores = collect(EvaluationTemplateItem::DOMAINS)
            ->mapWithKeys(function (string $label, string $domain) use ($evaluation) {
                $items = $evaluation->scores
                    ->filter(function (EvaluationScore $score) use ($domain) {
                        return $score->templateItem && $score->templateItem->domain === $domain;
                    })
                    ->sortBy(function (EvaluationScore $score) {
                        return $score->templateItem->sort_order;
                    })
                    ->values()
                    ->map(function (EvaluationScore $score) {
                        return [
                            'template_item_id' => $score->template_item_id,
                            'label' => $score->templateItem->label,
                            'score' => (float) $score->score,
                            'comment' => $score->comment,
                        ];
                    })
                    ->all();

                return ["scores_{$domain}" => $items];
            })
            ->all();

        return array_merge($scores, [
            'scale_min' => (float) $evaluation->template->scale_min,
            'scale_max' => (float) $evaluation->template->scale_max,
        ]);
    }

    protected static function scoreSections(): array
    {
        return collect(EvaluationTemplateItem::DOMAINS)
            ->map(function (string $label, string $domain) {
                return Forms\Components\Section::make($label)
                    ->schema([
                        Forms\Components\Repeater::make("scores_{$domain}")
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('template_item_id'),
                                Forms\Components\TextInput::make('label')
                                    ->label('Criterio')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('score')
                                    ->label('Nota')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Textarea::make('comment')
                                    ->label('Observacao')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->deletable(false)
                            ->addable(false)
                            ->reorderable(false),
                    ])
                    ->hidden(fn (Get $get) => ! $get('template_id'));
            })
            ->values()
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
