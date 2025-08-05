<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamsResource\Pages;
use App\Filament\Resources\ExamsResource\RelationManagers;
use App\Models\Exams;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ExamsResource extends Resource
{
    protected static ?string $model = Exams::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Examen';

    protected static ?string $pluralModelLabel = 'Examens';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de l\'examen')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Code de l\'examen')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('type')
                            ->label('Type d\'examen')
                            ->options([
                                'theoretical' => 'Théorique',
                                'practical' => 'Pratique',
                                'code' => 'Code de la route',
                                'driving' => 'Conduite',
                                'final' => 'Examen final',
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make('Contenu et Configuration')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->required()
                            ->maxLength(2000),
                        Forms\Components\Textarea::make('instructions')
                            ->label('Instructions')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\TextInput::make('duration')
                            ->label('Durée (minutes)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(480)
                            ->required(),
                        Forms\Components\TextInput::make('passing_score')
                            ->label('Score de réussite (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(70)
                            ->required(),
                    ])->columns(2),

                Section::make('Planning')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Heure de début')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('Heure de fin')
                            ->after('start_time')
                            ->required(),
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Nombre max de participants')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(20),
                    ])->columns(3),

                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'scheduled' => 'Programmé',
                                'active' => 'En cours',
                                'completed' => 'Terminé',
                                'cancelled' => 'Annulé',
                            ])
                            ->default('scheduled')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Début')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('passing_score')
                    ->label('Score de réussite')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Max participants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'info' => 'scheduled',
                        'success' => 'active',
                        'warning' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled' => 'Programmé',
                        'active' => 'En cours',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'theoretical' => 'Théorique',
                        'practical' => 'Pratique',
                        'code' => 'Code de la route',
                        'driving' => 'Conduite',
                        'final' => 'Examen final',
                    ]),
                Tables\Filters\Filter::make('start_time')
                    ->label('Date de début')
                    ->form([
                        Forms\Components\DateTimePicker::make('start_from')
                            ->label('À partir du'),
                        Forms\Components\DateTimePicker::make('start_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->where('start_time', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->where('start_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_time', 'desc');
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExams::route('/create'),
            'view' => Pages\ViewExams::route('/{record}'),
            'edit' => Pages\EditExams::route('/{record}/edit'),
        ];
    }
}
