<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Résultat';

    protected static ?string $pluralModelLabel = 'Résultats';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Étudiant')
                            ->options(function () {
                                return \App\Models\Student::with('user')->get()
                                    ->mapWithKeys(function ($student) {
                                        return [$student->id => $student->user->name . ' (' . $student->student_number . ')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('exam_id')
                            ->label('Examen')
                            ->options(function () {
                                return \App\Models\Exams::all()
                                    ->mapWithKeys(function ($exam) {
                                        return [$exam->id => $exam->title . ' (' . $exam->code . ')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Résultats')
                    ->schema([
                        Forms\Components\TextInput::make('score')
                            ->label('Score obtenu')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\TextInput::make('max_score')
                            ->label('Score maximum')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(100)
                            ->required(),
                        Forms\Components\TextInput::make('percentage')
                            ->label('Pourcentage (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'passed' => 'Réussi',
                                'failed' => 'Échoué',
                                'pending' => 'En attente',
                                'absent' => 'Absent',
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make('Détails')
                    ->schema([
                        Forms\Components\DateTimePicker::make('exam_date')
                            ->label('Date de l\'examen')
                            ->required(),
                        Forms\Components\Textarea::make('comments')
                            ->label('Commentaires')
                            ->rows(3)
                            ->maxLength(1000),
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
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Étudiant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam.title')
                    ->label('Examen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->label('Max')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Pourcentage')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_date')
                    ->label('Date examen')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'passed',
                        'danger' => 'failed',
                        'warning' => 'pending',
                        'gray' => 'absent',
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
                        'passed' => 'Réussi',
                        'failed' => 'Échoué',
                        'pending' => 'En attente',
                        'absent' => 'Absent',
                    ]),
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label('Examen')
                    ->relationship('exam', 'title'),
                Tables\Filters\Filter::make('exam_date')
                    ->label('Date d\'examen')
                    ->form([
                        Forms\Components\DateTimePicker::make('exam_from')
                            ->label('À partir du'),
                        Forms\Components\DateTimePicker::make('exam_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['exam_from'],
                                fn (Builder $query, $date): Builder => $query->where('exam_date', '>=', $date),
                            )
                            ->when(
                                $data['exam_until'],
                                fn (Builder $query, $date): Builder => $query->where('exam_date', '<=', $date),
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
            ->defaultSort('exam_date', 'desc');
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
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'view' => Pages\ViewResult::route('/{record}'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
}
