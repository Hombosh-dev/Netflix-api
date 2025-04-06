<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserListsResource\Pages;
use App\Filament\Resources\UserListsResource\RelationManagers;
use App\Models\UserList;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserListsResource extends Resource
{
    protected static ?string $model = UserList::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Інформація про користувача')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Користувач'),
                    ]),
                Forms\Components\Section::make('Деталі елемента списку')
                    ->schema([
                        Forms\Components\Select::make('listable_type')
                            ->options([
                                'App\\Models\\Movie' => 'Фільм',
                                'App\\Models\\Series' => 'Серіал',
                            ])
                            ->required()
                            ->label('Тип елемента'),
                        Forms\Components\TextInput::make('listable_id')
                            ->required()
                            ->maxLength(26)
                            ->label('ID елемента'),
                    ]),
                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'favorite' => 'Улюблене',
                                'watchlist' => 'Список перегляду',
                                // Додайте інші типи відповідно до вашого перерахування UserListType
                            ])
                            ->required()
                            ->label('Тип списку'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable(),
                Tables\Columns\TextColumn::make('listable_type')
                    ->label('Тип елемента')
                    ->searchable(),
                Tables\Columns\TextColumn::make('listable_id')
                    ->label('ID елемента')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип списку'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата створення')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата оновлення')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Визначте необхідні фільтри тут
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Редагувати'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Видалити'),
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
            'index' => Pages\ListUserLists::route('/'),
            'create' => Pages\CreateUserLists::route('/create'),
            'edit' => Pages\EditUserLists::route('/{record}/edit'),
        ];
    }
}
