<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Étudiant';

    protected static ?string $pluralModelLabel = 'Étudiants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Utilisateur')
                            ->options(function () {
                                return \App\Models\User::all()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => $user->name . ' (' . $user->email . ')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('student_number')
                            ->label('Numéro d\'étudiant')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
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
                    ])->columns(2),

                Section::make('Informations Académiques')
                    ->schema([
                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Date d\'inscription')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active' => 'Actif',
                                'inactive' => 'Inactif',
                                'graduated' => 'Diplômé',
                                'suspended' => 'Suspendu',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Select::make('license_type')
                            ->label('Type de permis')
                            ->options([
                                'A' => 'Permis A (Moto)',
                                'B' => 'Permis B (Voiture)',
                                'C' => 'Permis C (Poids lourd)',
                                'D' => 'Permis D (Transport en commun)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('progress_percentage')
                            ->label('Progression (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0),
                    ])->columns(2),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_number')
                    ->label('Numéro étudiant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_class.name')
                    ->label('Classe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Date d\'inscription')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_type')
                    ->label('Type de permis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progression')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                        'info' => 'graduated',
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
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'graduated' => 'Diplômé',
                        'suspended' => 'Suspendu',
                    ]),
                Tables\Filters\SelectFilter::make('license_type')
                    ->label('Type de permis')
                    ->options([
                        'A' => 'Permis A (Moto)',
                        'B' => 'Permis B (Voiture)',
                        'C' => 'Permis C (Poids lourd)',
                        'D' => 'Permis D (Transport en commun)',
                    ]),
                Tables\Filters\SelectFilter::make('academic_class_id')
                    ->label('Classe Académique')
                    ->relationship('academicClass', 'name'),
                Tables\Filters\Filter::make('enrollment_date')
                    ->label('Date d\'inscription')
                    ->form([
                        Forms\Components\DatePicker::make('enrollment_from')
                            ->label('À partir du'),
                        Forms\Components\DatePicker::make('enrollment_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['enrollment_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('enrollment_date', '>=', $date),
                            )
                            ->when(
                                $data['enrollment_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('enrollment_date', '<=', $date),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
