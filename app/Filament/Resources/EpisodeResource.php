<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Filament\Resources\EpisodeResource\RelationManagers;
use App\Models\Episode;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('movie_id')
                            ->label('ID фільму')
                            ->required()
                            ->maxLength(26),
                        TextInput::make('number')
                            ->label('Номер епізоду')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('name')
                            ->label('Назва епізоду')
                            ->required()
                            ->maxLength(128),
                        Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(512),
                        TextInput::make('duration')
                            ->label('Тривалість (хвилини)')
                            ->numeric()
                            ->minValue(1),
                        DatePicker::make('air_date')
                            ->label('Дата виходу'),
                        Toggle::make('is_filler')
                            ->label('Філер')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Медіа')
                    ->schema([
                        Repeater::make('pictures')
                            ->label('Зображення')
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL зображення')
                                    ->required()
                                    ->maxLength(2048),
                            ])
                            ->defaultItems(0)
                            ->columns(1),
                        Repeater::make('video_players')
                            ->label('Відеоплеєри')
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL відеоплеєра')
                                    ->required()
                                    ->maxLength(2048),
                            ])
                            ->defaultItems(0)
                            ->columns(1),
                    ])
                    ->columns(1),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta заголовок')
                            ->maxLength(128),
                        Textarea::make('meta_description')
                            ->label('Meta опис')
                            ->maxLength(376),
                        TextInput::make('meta_image')
                            ->label('Meta зображення')
                            ->maxLength(2048),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('movie_id')
                    ->label('ID фільму')
                    ->searchable(),
                TextColumn::make('number')
                    ->label('Номер епізоду')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Назва епізоду')
                    ->searchable(),
                BooleanColumn::make('is_filler')
                    ->label('Філер')
                    ->sortable(),
                TextColumn::make('air_date')
                    ->label('Дата виходу')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Дата створення')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Дата оновлення')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}
