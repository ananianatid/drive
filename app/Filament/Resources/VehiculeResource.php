<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculeResource\Pages;
use App\Filament\Resources\VehiculeResource\RelationManagers;
use App\Models\Vehicule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class VehiculeResource extends Resource
{
    protected static ?string $model = Vehicule::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Gestion des Cours';

    protected static ?string $modelLabel = 'Véhicule';

    protected static ?string $pluralModelLabel = 'Véhicules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du véhicule')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('brand')
                            ->label('Marque')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('model')
                            ->label('Modèle')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('year')
                            ->label('Année')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                    ])->columns(2),

                Section::make('Informations Administratives')
                    ->schema([
                        Forms\Components\TextInput::make('license_plate')
                            ->label('Plaque d\'immatriculation')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('vin')
                            ->label('Numéro VIN')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('color')
                            ->label('Couleur')
                            ->maxLength(50),
                    ])->columns(2),

                Section::make('Maintenance et Assurance')
                    ->schema([
                        Forms\Components\DatePicker::make('last_maintenance_date')
                            ->label('Dernière maintenance'),
                        Forms\Components\DatePicker::make('next_maintenance_date')
                            ->label('Prochaine maintenance'),
                        Forms\Components\DatePicker::make('insurance_expiry_date')
                            ->label('Expiration assurance')
                            ->required(),
                        Forms\Components\DatePicker::make('registration_expiry_date')
                            ->label('Expiration immatriculation')
                            ->required(),
                    ])->columns(2),

                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'available' => 'Disponible',
                                'in_use' => 'En cours d\'utilisation',
                                'maintenance' => 'En maintenance',
                                'out_of_service' => 'Hors service',
                            ])
                            ->default('available')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marque')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Année')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Plaque')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Couleur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_maintenance_date')
                    ->label('Dernière maintenance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_expiry_date')
                    ->label('Expiration assurance')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'in_use',
                        'danger' => 'maintenance',
                        'gray' => 'out_of_service',
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
                        'available' => 'Disponible',
                        'in_use' => 'En cours d\'utilisation',
                        'maintenance' => 'En maintenance',
                        'out_of_service' => 'Hors service',
                    ]),
                Tables\Filters\SelectFilter::make('brand')
                    ->label('Marque')
                    ->options([
                        'Renault' => 'Renault',
                        'Peugeot' => 'Peugeot',
                        'Citroën' => 'Citroën',
                        'Ford' => 'Ford',
                        'Volkswagen' => 'Volkswagen',
                    ]),
                Tables\Filters\Filter::make('maintenance_due')
                    ->label('Maintenance due')
                    ->query(fn (Builder $query): Builder => $query->where('next_maintenance_date', '<=', now())),
                Tables\Filters\Filter::make('insurance_expiring')
                    ->label('Assurance expirant')
                    ->query(fn (Builder $query): Builder => $query->where('insurance_expiry_date', '<=', now()->addDays(30))),
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
            'index' => Pages\ListVehicules::route('/'),
            'create' => Pages\CreateVehicule::route('/create'),
            'view' => Pages\ViewVehicule::route('/{record}'),
            'edit' => Pages\EditVehicule::route('/{record}/edit'),
        ];
    }
}
