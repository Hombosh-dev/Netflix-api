<?php

namespace App\Filament\Resources\SelectionResource\RelationManagers;

use App\Enums\Kind;
use App\Enums\Status;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                // No additional fields needed for the morph relationship
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
                    ->searchable()
                    ->sortable(),

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
            ])
            ->headerActions([
                AttachAction::make()
                    ->label(__('Додати фільми'))
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'original_name'])
            ])
            ->actions([
                DetachAction::make()
                    ->label(__('Видалити з підбірки')),
            ])
            ->bulkActions([
                DetachBulkAction::make()
                    ->label(__('Видалити вибрані')),
            ]);
    }
}
