<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IdentityCardResource\Pages;
use App\Filament\Resources\IdentityCardResource\RelationManagers;
use App\Models\IdentityCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class IdentityCardResource extends Resource
{
    protected static ?string $model = IdentityCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Gestion Académique';

    protected static ?string $modelLabel = 'Carte d\'Identité';

    protected static ?string $pluralModelLabel = 'Cartes d\'Identité';

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
                        Forms\Components\TextInput::make('card_number')
                            ->label('Numéro de carte')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('type')
                            ->label('Type de carte')
                            ->options([
                                'student' => 'Carte étudiant',
                                'temporary' => 'Carte temporaire',
                                'replacement' => 'Carte de remplacement',
                            ])
                            ->default('student')
                            ->required(),
                    ])->columns(2),

                Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Date d\'émission')
                            ->required(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Date d\'expiration')
                            ->after('issue_date')
                            ->required(),
                        Forms\Components\DatePicker::make('replacement_date')
                            ->label('Date de remplacement'),
                    ])->columns(3),

                Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active' => 'Active',
                                'expired' => 'Expirée',
                                'lost' => 'Perdue',
                                'stolen' => 'Volée',
                                'replaced' => 'Remplacée',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->required(),
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
                Tables\Columns\TextColumn::make('card_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Étudiant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Émission')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiration')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'lost',
                        'gray' => 'stolen',
                        'info' => 'replaced',
                    ]),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expirée',
                        'lost' => 'Perdue',
                        'stolen' => 'Volée',
                        'replaced' => 'Remplacée',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'student' => 'Carte étudiant',
                        'temporary' => 'Carte temporaire',
                        'replacement' => 'Carte de remplacement',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expirant bientôt')
                    ->query(fn (Builder $query): Builder => $query->where('expiry_date', '<=', now()->addDays(30))),
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
            'index' => Pages\ListIdentityCards::route('/'),
            'create' => Pages\CreateIdentityCard::route('/create'),
            'view' => Pages\ViewIdentityCard::route('/{record}'),
            'edit' => Pages\EditIdentityCard::route('/{record}/edit'),
        ];
    }
}
