<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudioResource\Pages;
use App\Filament\Resources\StudioResource\RelationManagers;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Основна інформація')
                ->schema([
                    TextInput::make('name')
                        ->label('Назва')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('description')
                        ->label('Опис')
                        ->required()
                        ->maxLength(512),
                    Forms\Components\FileUpload::make('image')
                        ->label('Зображення')
                        ->image(),
                    Repeater::make('aliases')
                        ->label('Альтернативні назви')
                        ->schema([
                            TextInput::make('alias')
                                ->label('Псевдонім')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(1)
                        ->orderable()
                        ->collapsed(),
                    Forms\Components\Toggle::make('is_genre')
                        ->label('Чи є жанром')
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Мета-дані')
                ->schema([
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(128),
                    TextInput::make('meta_title')
                        ->label('Мета-заголовок')
                        ->maxLength(64),
                    TextInput::make('meta_description')
                        ->label('Мета-опис')
                        ->maxLength(192),
                    Forms\Components\FileUpload::make('meta_image')
                        ->label('Мета-зображення')
                        ->image(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('Назва')
                ->searchable(),
            TextColumn::make('description')
                ->label('Опис')
                ->searchable(),
            ImageColumn::make('image')
                ->label('Зображення'),
            IconColumn::make('is_genre')
                ->label('Жанр')
                ->boolean(),
            TextColumn::make('slug')
                ->label('Slug')
                ->searchable(),
            TextColumn::make('meta_title')
                ->label('Мета-заголовок')
                ->searchable(),
            TextColumn::make('meta_description')
                ->label('Мета-опис')
                ->searchable(),
            ImageColumn::make('meta_image')
                ->label('Мета-зображення'),
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
        ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Редагувати'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Видалити'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }
}
