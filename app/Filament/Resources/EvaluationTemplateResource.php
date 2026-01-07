<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationTemplateResource\Pages;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationTemplateItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class EvaluationTemplateResource extends Resource
{
    protected static ?string $model = EvaluationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sport')
                            ->label('Esporte')
                            ->required()
                            ->maxLength(255)
                            ->default('Futebol'),
                        Forms\Components\TextInput::make('scale_min')
                            ->label('Escala minima')
                            ->numeric()
                            ->required()
                            ->default(1),
                        Forms\Components\TextInput::make('scale_max')
                            ->label('Escala maxima')
                            ->numeric()
                            ->required()
                            ->default(10),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Template padrao'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Criterios')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->label('Itens')
                            ->schema([
                                Forms\Components\Select::make('domain')
                                    ->label('Dominio')
                                    ->options(EvaluationTemplateItem::DOMAINS)
                                    ->required(),
                                Forms\Components\TextInput::make('label')
                                    ->label('Criterio')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('weight')
                                    ->label('Peso')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                                Forms\Components\Hidden::make('sort_order')
                                    ->default(0),
                            ])
                            ->orderable('sort_order')
                            ->addActionLabel('Adicionar criterio')
                            ->columns(3)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sport')
                    ->label('Esporte')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('organization_id')
                    ->label('Escopo')
                    ->formatStateUsing(fn ($state) => $state ? 'Organizacao' : 'Global')
                    ->colors([
                        'primary' => fn ($state) => ! $state,
                        'success' => fn ($state) => (bool) $state,
                    ]),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Padrao')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\Action::make('clone')
                    ->label('Clonar para minha organizacao')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->visible(fn (EvaluationTemplate $record) => ! $record->organization_id)
                    ->action(function (EvaluationTemplate $record): void {
                        $organizationId = auth()->user()->organization_id;

                        DB::transaction(function () use ($record, $organizationId) {
                            $clone = $record->replicate(['organization_id', 'is_default']);
                            $clone->organization_id = $organizationId;
                            $clone->is_default = false;
                            $clone->save();

                            $record->items->each(function (EvaluationTemplateItem $item) use ($clone) {
                                $clone->items()->create([
                                    'domain' => $item->domain,
                                    'label' => $item->label,
                                    'weight' => $item->weight,
                                    'sort_order' => $item->sort_order,
                                ]);
                            });
                        });

                        Notification::make()
                            ->title('Template clonado com sucesso.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn (EvaluationTemplate $record) => (bool) $record->organization_id),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (EvaluationTemplate $record) => (bool) $record->organization_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationTemplates::route('/'),
            'create' => Pages\CreateEvaluationTemplate::route('/create'),
            'edit' => Pages\EditEvaluationTemplate::route('/{record}/edit'),
        ];
    }
}
