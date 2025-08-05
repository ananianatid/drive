<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicClassResource\Pages;
use App\Filament\Resources\AcademicClassResource\RelationManagers;
use App\Models\AcademicClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;

class AcademicClassResource extends Resource
{
    protected static ?string $model = AcademicClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Classe Académique';

    protected static ?string $pluralModelLabel = 'Classes Académiques';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de la classe')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000),
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
                    ])->columns(2),

                Section::make('Planning')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Date de début')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Date de fin')
                            ->after('start_date'),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Capacité')
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
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'completed' => 'Terminée',
                                'suspended' => 'Suspendue',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Date de début')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Date de fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacité')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Étudiants inscrits')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                        'info' => 'completed',
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
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Terminée',
                        'suspended' => 'Suspendue',
                    ]),
                Tables\Filters\Filter::make('start_date')
                    ->label('Date de début')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('À partir du'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            // RelationManagers\StudentsRelationManager::class,
            // RelationManagers\CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicClasses::route('/'),
            'create' => Pages\CreateAcademicClass::route('/create'),
            'view' => Pages\ViewAcademicClass::route('/{record}'),
            'edit' => Pages\EditAcademicClass::route('/{record}/edit'),
        ];
    }
}
