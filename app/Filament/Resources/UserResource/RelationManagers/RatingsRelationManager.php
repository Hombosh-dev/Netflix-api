<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Оцінки');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('movie_id')
                    ->label(__('Фільм'))
                    ->relationship('movie', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('number')
                    ->label(__('Оцінка'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->step(1),
                    
                Textarea::make('review')
                    ->label(__('Відгук'))
                    ->nullable()
                    ->rows(5),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('movie.name')
                    ->label(__('Фільм'))
                    ->description(fn ($record) => $record->movie?->kind?->getLabel())
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('number')
                    ->label(__('Оцінка'))
                    ->formatStateUsing(fn (int $state) => $state . '/10')
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    }),
                    
                TextColumn::make('review')
                    ->label(__('Відгук'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->review)
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('number')
                    ->label(__('Оцінка'))
                    ->form([
                        TextInput::make('min_rating')
                            ->label(__('Мінімальна оцінка'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        TextInput::make('max_rating')
                            ->label(__('Максимальна оцінка'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_rating'],
                                fn (Builder $query, $rating): Builder => $query->where('number', '>=', $rating),
                            )
                            ->when(
                                $data['max_rating'],
                                fn (Builder $query, $rating): Builder => $query->where('number', '<=', $rating),
                            );
                    }),
                    
                Filter::make('has_review')
                    ->label(__('З відгуком'))
                    ->query(fn (Builder $query): Builder => $query->withReviews())
                    ->toggle(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Додати оцінку')),
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
