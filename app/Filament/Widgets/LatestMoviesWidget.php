<?php

namespace App\Filament\Widgets;

use App\Models\Movie;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestMoviesWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Останні додані фільми'))
            ->query(
                Movie::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('meta_image')
                    ->label(__('Постер'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Movie $record) => $record->kind?->getLabel()),

                TextColumn::make('imdb_score')
                    ->label(__('IMDb'))
                    ->sortable()
                    ->color(fn ($state) => match(true) {
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label(__('Додано'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('Переглянути'))
                    ->url(fn (Movie $record) => route('filament.admin.resources.movies.edit', $record))
                    ->icon('heroicon-m-eye')
                    ->openUrlInNewTab(),
            ]);
    }
}
