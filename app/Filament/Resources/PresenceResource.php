<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresenceResource\Pages;
use App\Filament\Resources\PresenceResource\RelationManagers;
use App\Models\Presence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class PresenceResource extends Resource
{
    protected static ?string $model = Presence::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Présence';

    protected static ?string $pluralModelLabel = 'Présences';

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
                        Forms\Components\Select::make('course_id')
                            ->label('Cours')
                            ->options(function () {
                                return \App\Models\Course::all()
                                    ->mapWithKeys(function ($course) {
                                        return [$course->id => $course->title . ' (' . $course->date . ')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Présence')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'present' => 'Présent',
                                'absent' => 'Absent',
                                'late' => 'En retard',
                                'excused' => 'Excusé',
                            ])
                            ->default('present')
                            ->required(),
                        Forms\Components\DateTimePicker::make('arrival_time')
                            ->label('Heure d\'arrivée'),
                        Forms\Components\DateTimePicker::make('departure_time')
                            ->label('Heure de départ')
                            ->after('arrival_time'),
                        Forms\Components\TextInput::make('duration')
                            ->label('Durée (minutes)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(480),
                    ])->columns(2),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Textarea::make('excuse')
                            ->label('Justificatif')
                            ->rows(2)
                            ->maxLength(500),
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
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Cours')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.date')
                    ->label('Date du cours')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('Arrivée')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Départ')
                    ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'excused',
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
                        'present' => 'Présent',
                        'absent' => 'Absent',
                        'late' => 'En retard',
                        'excused' => 'Excusé',
                    ]),
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Étudiant')
                    ->relationship('student.user', 'name'),
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Cours')
                    ->relationship('course', 'title'),
                Tables\Filters\Filter::make('course_date')
                    ->label('Date du cours')
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
                                fn (Builder $query, $date): Builder => $query->whereHas('course', fn ($q) => $q->whereDate('date', '>=', $date)),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereHas('course', fn ($q) => $q->whereDate('date', '<=', $date)),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPresences::route('/'),
            'create' => Pages\CreatePresence::route('/create'),
            'view' => Pages\ViewPresence::route('/{record}'),
            'edit' => Pages\EditPresence::route('/{record}/edit'),
        ];
    }
}
