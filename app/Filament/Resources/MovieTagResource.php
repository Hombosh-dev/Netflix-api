<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovieTagResource\Pages;
use App\Filament\Resources\MovieTagResource\RelationManagers;
use App\Models\MovieTag;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovieTagResource extends Resource
{
    protected static ?string $model = MovieTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('movie_id')
                ->label('Movie')
                ->relationship('movie', 'name')
                ->required(),
            Select::make('tag_id')
                ->label('Tag')
                ->relationship('tag', 'name')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('movie.name')
                ->label('Movie')
                ->sortable()
                ->searchable(),
            TextColumn::make('tag.name')
                ->label('Tag')
                ->sortable()
                ->searchable(),
        ])
            ->filters([])
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
            'index'  => Pages\ListMovieTags::route('/'),
            'create' => Pages\CreateMovieTag::route('/create'),
            'edit'   => Pages\EditMovieTag::route('/{record}/edit'),
        ];
    }
}
