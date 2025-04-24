<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCommentsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Останні коментарі'))
            ->query(
                Comment::query()
                    ->with(['user', 'commentable'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Користувач'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('content')
                    ->label(__('Коментар'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('commentable_type')
                    ->label(__('Тип'))
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\\Models\\Movie' => __('Фільм'),
                            'App\\Models\\Episode' => __('Епізод'),
                            'App\\Models\\Selection' => __('Підбірка'),
                            default => $state,
                        };
                    }),

                IconColumn::make('is_spoiler')
                    ->label(__('Спойлер'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('Додано'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('Переглянути'))
                    ->url(fn (Comment $record) => route('filament.admin.resources.comments.edit', $record))
                    ->icon('heroicon-m-eye')
                    ->openUrlInNewTab(),
            ]);
    }
}
