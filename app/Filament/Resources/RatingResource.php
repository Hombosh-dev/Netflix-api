<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingsResource\Pages;
use App\Filament\Resources\RatingsResource\RelationManagers;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('movie_id')
                    ->label('Фільм')
                    ->relationship('movie', 'name')
                    ->required(),
                TextInput::make('number')
                    ->label('Оцінка')
                    ->required()
                    ->numeric(),
                Textarea::make('review')
                    ->label('Відгук')
                    ->columnSpanFull(),
            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable(),
                TextColumn::make('movie.name')
                    ->label('Фільм')
                    ->searchable(),
                TextColumn::make('number')
                    ->label('Оцінка')
                    ->numeric()
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
                EditAction::make()
                    ->label('Редагувати'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Видалити обране'),
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
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRatings::route('/create'),
            'edit' => Pages\EditRatings::route('/{record}/edit'),
        ];
    }
}
