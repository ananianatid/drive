<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Instructeur';

    protected static ?string $pluralModelLabel = 'Instructeurs';

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
                        Forms\Components\TextInput::make('employee_number')
                            ->label('Numéro d\'employé')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('specialization')
                            ->label('Spécialisation')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Informations Professionnelles')
                    ->schema([
                        Forms\Components\DatePicker::make('hire_date')
                            ->label('Date d\'embauche')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active' => 'Actif',
                                'inactive' => 'Inactif',
                                'suspended' => 'Suspendu',
                                'retired' => 'Retraité',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\TextInput::make('experience_years')
                            ->label('Années d\'expérience')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50),
                        Forms\Components\TextInput::make('certification_number')
                            ->label('Numéro de certification')
                            ->maxLength(100),
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
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('Numéro employé')
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
                Tables\Columns\TextColumn::make('specialization')
                    ->label('Spécialisation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Date d\'embauche')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('experience_years')
                    ->label('Expérience')
                    ->suffix(' ans')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certification_number')
                    ->label('Certification')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                        'info' => 'retired',
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
                        'suspended' => 'Suspendu',
                        'retired' => 'Retraité',
                    ]),
                Tables\Filters\SelectFilter::make('specialization')
                    ->label('Spécialisation')
                    ->options([
                        'Conduite' => 'Conduite',
                        'Code de la route' => 'Code de la route',
                        'Mécanique' => 'Mécanique',
                        'Sécurité routière' => 'Sécurité routière',
                    ]),
                Tables\Filters\Filter::make('hire_date')
                    ->label('Date d\'embauche')
                    ->form([
                        Forms\Components\DatePicker::make('hire_from')
                            ->label('À partir du'),
                        Forms\Components\DatePicker::make('hire_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hire_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('hire_date', '>=', $date),
                            )
                            ->when(
                                $data['hire_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('hire_date', '<=', $date),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
