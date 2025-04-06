<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovieNotificationsResource\Pages;
use App\Filament\Resources\MovieNotificationsResource\RelationManagers;
use App\Models\MovieNotifications;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovieNotificationsResource extends Resource
{
    protected static ?string $model = MovieNotifications::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('movie_id')
                    ->label('Movie')
                    ->relationship('movie', 'name')
                    ->required(),
                DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->disabled(),
                DateTimePicker::make('updated_at')
                    ->label('Updated At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')
                ->label('User')
                ->sortable()
                ->searchable(),
            TextColumn::make('movie.name')
                ->label('Movie')
                ->sortable()
                ->searchable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMovieNotifications::route('/'),
            'create' => Pages\CreateMovieNotifications::route('/create'),
            'edit'   => Pages\EditMovieNotifications::route('/{record}/edit'),
        ];
    }
}
