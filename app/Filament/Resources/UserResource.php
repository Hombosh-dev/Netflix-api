<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ім\'я')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Електронна пошта')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at')
                            ->label('Дата підтвердження електронної пошти'),
                        TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Додаткова інформація')
                    ->schema([
                        TextInput::make('role')
                            ->label('Роль')
                            ->required(),
                        TextInput::make('gender')
                            ->label('Стать'),
                        TextInput::make('avatar')
                            ->label('Аватар')
                            ->maxLength(2048),
                        TextInput::make('backdrop')
                            ->label('Фонове зображення')
                            ->maxLength(2048),
                        Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(248),
                        DatePicker::make('birthday')
                            ->label('Дата народження'),
                    ]),

                Section::make('Налаштування')
                    ->schema([
                        Toggle::make('allow_adult')
                            ->label('Дозволити контент для дорослих')
                            ->required(),
                        Toggle::make('is_auto_next')
                            ->label('Автоматичний перехід до наступного')
                            ->required(),
                        Toggle::make('is_auto_play')
                            ->label('Автоматичне відтворення')
                            ->required(),
                        Toggle::make('is_auto_skip_intro')
                            ->label('Автоматичне пропускання вступу')
                            ->required(),
                        Toggle::make('is_private_favorites')
                            ->label('Приватні обрані')
                            ->required(),
                    ]),

                Section::make('Інша інформація')
                    ->schema([
                        DateTimePicker::make('last_seen_at')
                            ->label('Останній вхід'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Електронна пошта')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label('Дата підтвердження')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->label('Роль'),
                TextColumn::make('gender')
                    ->label('Стать'),
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('avatar')
                    ->label('Аватар')
                    ->searchable(),
                TextColumn::make('backdrop')
                    ->label('Фонове зображення')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Опис')
                    ->searchable(),
                TextColumn::make('birthday')
                    ->label('Дата народження')
                    ->date()
                    ->sortable(),
                IconColumn::make('allow_adult')
                    ->label('Дозволити контент для дорослих')
                    ->boolean(),
                TextColumn::make('last_seen_at')
                    ->label('Останній вхід')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_auto_next')
                    ->label('Автоматичний перехід до наступного')
                    ->boolean(),
                IconColumn::make('is_auto_play')
                    ->label('Автоматичне відтворення')
                    ->boolean(),
                IconColumn::make('is_auto_skip_intro')
                    ->label('Автоматичне пропускання вступу')
                    ->boolean(),
                IconColumn::make('is_private_favorites')
                    ->label('Приватні обрані')
                    ->boolean(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
