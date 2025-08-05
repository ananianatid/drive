<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Filament\Resources\LicenseResource\RelationManagers;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $modelLabel = 'Type de Permis';

    protected static ?string $pluralModelLabel = 'Types de Permis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du permis')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Code du permis')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10),
                        Forms\Components\TextInput::make('display_name')
                            ->label('Nom d\'affichage')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('validity_period_years')
                            ->label('Période de validité (années)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(10)
                            ->required(),
                        Forms\Components\TextInput::make('training_hours')
                            ->label('Heures de formation requises')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->required(),
                        Forms\Components\TextInput::make('minimum_age')
                            ->label('Âge minimum')
                            ->numeric()
                            ->minValue(16)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Textarea::make('requirements')
                            ->label('Exigences')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Nom d\'affichage')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity_period_years')
                    ->label('Validité')
                    ->suffix(' ans')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('training_hours')
                    ->label('Formation')
                    ->suffix(' h')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_age')
                    ->label('Âge min')
                    ->suffix(' ans')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
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
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'view' => Pages\ViewLicense::route('/{record}'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
