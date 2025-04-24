<?php

namespace App\Filament\Resources\CommentResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LikesRelationManager extends RelationManager
{
    protected static string $relationship = 'likes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Toggle::make('is_liked')
                    ->label(__('Лайк/Дизлайк'))
                    ->onIcon('heroicon-o-hand-thumb-up')
                    ->offIcon('heroicon-o-hand-thumb-down')
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Користувач'))
                    ->description(fn ($record) => '@' . ($record->user->username ?? 'unknown'))
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_liked')
                    ->label(__('Тип'))
                    ->onIcon('heroicon-s-hand-thumb-up')
                    ->offIcon('heroicon-s-hand-thumb-down')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_liked')
                    ->label(__('Тип реакції'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Лайки'))
                    ->falseLabel(__('Дизлайки'))
                    ->indicator(__('Тип реакції')),

                SelectFilter::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Користувач')),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
