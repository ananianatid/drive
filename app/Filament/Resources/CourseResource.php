<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestion des Cours';

    protected static ?string $modelLabel = 'Cours';

    protected static ?string $pluralModelLabel = 'Cours';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre du cours')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Select::make('academic_class_id')
                            ->label('Classe Académique')
                            ->options(function () {
                                return \App\Models\AcademicClass::all()
                                    ->mapWithKeys(function ($class) {
                                        return [$class->id => $class->name];
                                    });
                            })
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('lesson_id')
                            ->label('Leçon')
                            ->options(function () {
                                return \App\Models\Lesson::all()
                                    ->mapWithKeys(function ($lesson) {
                                        return [$lesson->id => $lesson->title];
                                    });
                            })
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Planning')
                    ->schema([
                        Forms\Components\Select::make('teacher_id')
                            ->label('Instructeur')
                            ->options(function () {
                                return \App\Models\Teacher::with('user')
                                    ->get()
                                    ->mapWithKeys(function ($teacher) {
                                        return [$teacher->id => $teacher->user->name ?? 'N/A'];
                                    });
                            })
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('vehicule_id')
                            ->label('Véhicule')
                            ->options(function () {
                                return \App\Models\Vehicule::all()
                                    ->mapWithKeys(function ($vehicule) {
                                        return [$vehicule->id => $vehicule->name . ' - ' . $vehicule->license_plate];
                                    });
                            })
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required(),
                    ])->columns(3),

                Section::make('Horaires')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Heure de début')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('Heure de fin')
                            ->after('start_time')
                            ->required(),
                        Forms\Components\TextInput::make('duration')
                            ->label('Durée (minutes)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(480),
                    ])->columns(3),

                Section::make('Configuration')
                    ->schema([
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_class.name')
                    ->label('Classe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lesson.title')
                    ->label('Leçon')
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
                Tables\Filters\SelectFilter::make('academic_class_id')
                    ->label('Classe Académique')
                    ->relationship('academicClass', 'name'),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
