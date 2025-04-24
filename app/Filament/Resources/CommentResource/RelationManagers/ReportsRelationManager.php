<?php

namespace App\Filament\Resources\CommentResource\RelationManagers;

use App\Enums\CommentReportType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->label(__('Тип скарги'))
                    ->options(CommentReportType::class)
                    ->enum(CommentReportType::class)
                    ->required(),

                Toggle::make('is_viewed')
                    ->label(__('Переглянуто'))
                    ->onIcon('heroicon-o-eye')
                    ->offIcon('heroicon-o-eye-slash')
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),

                Textarea::make('body')
                    ->label(__('Опис скарги'))
                    ->placeholder(__('Детальний опис проблеми'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Скаржник'))
                    ->description(fn ($record) => '@' . ($record->user->username ?? 'unknown'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('Тип скарги'))
                    ->badge()
                    ->formatStateUsing(fn (CommentReportType $state): string => $state->getLabel())
                    ->color(fn (CommentReportType $state): string => $state->getColor())
                    ->icon(fn (CommentReportType $state): string => $state->getIcon())
                    ->sortable(),

                ToggleColumn::make('is_viewed')
                    ->label(__('Переглянуто'))
                    ->onIcon('heroicon-s-eye')
                    ->offIcon('heroicon-s-eye-slash')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_viewed')
                    ->label(__('Статус перегляду'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Переглянуті'))
                    ->falseLabel(__('Непереглянуті'))
                    ->indicator(__('Статус перегляду')),

                SelectFilter::make('user_id')
                    ->label(__('Скаржник'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Скаржник')),

                SelectFilter::make('type')
                    ->label(__('Тип скарги'))
                    ->options(CommentReportType::class)
                    ->multiple()
                    ->indicator(__('Тип скарги')),
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
