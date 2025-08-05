<?php

namespace App\Filament\Resources\AcademicClassResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000),
                Forms\Components\Select::make('lesson_id')
                    ->label('Leçon')
                    ->relationship('lesson', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Instructeur')
                    ->relationship('teacher', 'user.name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('vehicule_id')
                    ->label('Véhicule')
                    ->relationship('vehicule', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Heure de début')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Heure de fin')
                    ->after('start_time')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->label('Durée (minutes)')
                    ->numeric()
                    ->minValue(15)
                    ->maxValue(480),
                Forms\Components\TextInput::make('max_students')
                    ->label('Nombre max d\'étudiants')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(4),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lesson.title')
                    ->label('Leçon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.user.name')
                    ->label('Instructeur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicule.name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Début')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->label('Max étudiants')
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
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Instructeur')
                    ->relationship('teacher', 'user.name'),
                Tables\Filters\SelectFilter::make('vehicule_id')
                    ->label('Véhicule')
                    ->relationship('vehicule', 'name'),
                Tables\Filters\Filter::make('date')
                    ->label('Date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('À partir du'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ]);
    }
}
