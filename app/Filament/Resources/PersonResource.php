<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\People;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = People::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Псевдонім')
                    ->required()
                    ->maxLength(128),
                TextInput::make('original_name')
                    ->label('Справжнє імʼя')
                    ->maxLength(128),
                TextInput::make('image')
                    ->label('Зображення')
                    ->maxLength(2048),
                TextInput::make('description')
                    ->label('Опис')
                    ->maxLength(512),
                DatePicker::make('birthday')
                    ->label('Дата народження'),
                TextInput::make('birthplace')
                    ->label('Місце народження')
                    ->maxLength(248),
                TextInput::make('meta_title')
                    ->label('Meta заголовок')
                    ->maxLength(128),
                TextInput::make('meta_description')
                    ->label('Meta опис')
                    ->maxLength(376),
                TextInput::make('meta_image')
                    ->label('Meta зображення')
                    ->maxLength(2048),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('URL-ідентифікатор')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable(),
                TextColumn::make('original_name')
                    ->label('Оригінальна назва')
                    ->searchable(),
                TextColumn::make('birthday')
                    ->label('Дата народження')
                    ->date()
                    ->sortable(),
                TextColumn::make('birthplace')
                    ->label('Місце народження')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Дата створення')
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
