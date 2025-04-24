<?php

namespace App\Filament\Resources\StudioResource\RelationManagers;

use App\Enums\Kind;
use App\Enums\Status;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MoviesRelationManager extends RelationManager
{
    protected static string $relationship = 'movies';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Фільми');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Назва'))
                    ->required()
                    ->maxLength(255),

                Select::make('kind')
                    ->label(__('Тип'))
                    ->options(Kind::class)
                    ->required(),

                Select::make('status')
                    ->label(__('Статус'))
                    ->options(Status::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image_name')
                    ->label(__('Постер'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->description(fn($record) => $record->full_title)
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('kind')
                    ->label(__('Тип'))
                    ->badge()
                    ->color(fn(Kind $state): string => match ($state) {
                        Kind::MOVIE => 'success',
                        Kind::TV_SERIES => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Статус'))
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('Опубліковано'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('Додано'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kind')
                    ->label(__('Тип'))
                    ->options(Kind::class)
                    ->multiple()
                    ->indicator(__('Тип')),

                SelectFilter::make('status')
                    ->label(__('Статус'))
                    ->options(Status::class)
                    ->multiple()
                    ->indicator(__('Статус')),

                TernaryFilter::make('is_published')
                    ->label(__('Статус публікації'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Опубліковані'))
                    ->falseLabel(__('Неопубліковані'))
                    ->indicator(__('Статус')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Додати фільм')),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('Переглянути')),
                EditAction::make()
                    ->label(__('Редагувати')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
