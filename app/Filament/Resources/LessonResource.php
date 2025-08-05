<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Filament\Resources\LessonResource\RelationManagers;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Gestion des Cours';

    protected static ?string $modelLabel = 'Leçon';

    protected static ?string $pluralModelLabel = 'Leçons';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de Base')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de la leçon')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Code de la leçon')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('difficulty')
                            ->label('Niveau de difficulté')
                            ->options([
                                'beginner' => 'Débutant',
                                'intermediate' => 'Intermédiaire',
                                'advanced' => 'Avancé',
                                'expert' => 'Expert',
                            ])
                            ->default('beginner')
                            ->required(),
                    ])->columns(2),

                Section::make('Contenu')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->required()
                            ->maxLength(2000),
                        Forms\Components\Textarea::make('content')
                            ->label('Contenu détaillé')
                            ->rows(6)
                            ->maxLength(5000),
                        Forms\Components\Textarea::make('objectives')
                            ->label('Objectifs d\'apprentissage')
                            ->rows(3)
                            ->maxLength(1000),
                    ])->columns(1),

                Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('duration')
                            ->label('Durée (minutes)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(480)
                            ->required(),
                        Forms\Components\TextInput::make('order')
                            ->label('Ordre dans le programme')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'draft' => 'Brouillon',
                                'archived' => 'Archivée',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(3),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes additionnelles')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->label('Difficulté')
                    ->colors([
                        'success' => 'beginner',
                        'warning' => 'intermediate',
                        'danger' => 'advanced',
                        'gray' => 'expert',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'draft',
                        'gray' => 'archived',
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
                        'draft' => 'Brouillon',
                        'archived' => 'Archivée',
                    ]),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Difficulté')
                    ->options([
                        'beginner' => 'Débutant',
                        'intermediate' => 'Intermédiaire',
                        'advanced' => 'Avancé',
                        'expert' => 'Expert',
                    ]),
                Tables\Filters\Filter::make('duration')
                    ->label('Durée')
                    ->form([
                        Forms\Components\TextInput::make('duration_min')
                            ->label('Durée minimum (minutes)')
                            ->numeric(),
                        Forms\Components\TextInput::make('duration_max')
                            ->label('Durée maximum (minutes)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['duration_min'],
                                fn (Builder $query, $duration): Builder => $query->where('duration', '>=', $duration),
                            )
                            ->when(
                                $data['duration_max'],
                                fn (Builder $query, $duration): Builder => $query->where('duration', '<=', $duration),
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
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'view' => Pages\ViewLesson::route('/{record}'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
