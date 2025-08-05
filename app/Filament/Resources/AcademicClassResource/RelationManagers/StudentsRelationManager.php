<?php

namespace App\Filament\Resources\AcademicClassResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $recordTitleAttribute = 'student_number';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('student_number')
                    ->label('Numéro d\'étudiant')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\DatePicker::make('enrollment_date')
                    ->label('Date d\'inscription')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'suspended' => 'Suspendu',
                        'graduated' => 'Diplômé',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\Select::make('license_type')
                    ->label('Type de licence')
                    ->options([
                        'A' => 'Permis A (Moto)',
                        'B' => 'Permis B (Voiture)',
                        'C' => 'Permis C (Poids lourd)',
                        'D' => 'Permis D (Transport de personnes)',
                    ]),
                Forms\Components\TextInput::make('progress_percentage')
                    ->label('Progression (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->maxLength(1000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student_number')
            ->columns([
                Tables\Columns\TextColumn::make('student_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Date d\'inscription')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                        'info' => 'graduated',
                    ]),
                Tables\Columns\TextColumn::make('license_type')
                    ->label('Type de licence')
                    ->searchable(),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progression')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
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
                        'graduated' => 'Diplômé',
                    ]),
                Tables\Filters\SelectFilter::make('license_type')
                    ->label('Type de licence')
                    ->options([
                        'A' => 'Permis A (Moto)',
                        'B' => 'Permis B (Voiture)',
                        'C' => 'Permis C (Poids lourd)',
                        'D' => 'Permis D (Transport de personnes)',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
