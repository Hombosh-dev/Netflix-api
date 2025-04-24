<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Останні зареєстровані користувачі'))
            ->query(
                User::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('Аватар'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Ім\'я'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record) => $record->email),

                TextColumn::make('role')
                    ->label(__('Роль'))
                    ->badge()
                    ->color(fn ($state) => $state?->getColor())
                    ->icon(fn ($state) => $state?->getIcon()),

                IconColumn::make('is_banned')
                    ->label(__('Заблокований'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('Зареєстрований'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('Переглянути'))
                    ->url(fn (User $record) => route('filament.admin.resources.users.edit', $record))
                    ->icon('heroicon-m-eye')
                    ->openUrlInNewTab(),
            ]);
    }
}
