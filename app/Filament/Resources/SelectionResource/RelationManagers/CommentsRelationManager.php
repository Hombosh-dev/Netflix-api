<?php

namespace App\Filament\Resources\SelectionResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'body';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Коментарі');
    }

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

                Textarea::make('body')
                    ->label(__('Текст коментаря'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('is_spoiler')
                    ->label(__('Містить спойлер'))
                    ->helperText(__('Позначте, якщо коментар містить спойлери'))
                    ->onIcon('heroicon-o-exclamation-triangle')
                    ->offIcon('heroicon-o-check-circle')
                    ->onColor('danger')
                    ->offColor('success')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Користувач'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->label(__('Коментар'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->body)
                    ->searchable(),

                IconColumn::make('is_spoiler')
                    ->label(__('Спойлер'))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_spoiler')
                    ->label(__('Спойлери'))
                    ->placeholder(__('Всі коментарі'))
                    ->trueLabel(__('Тільки зі спойлерами'))
                    ->falseLabel(__('Без спойлерів'))
                    ->indicator(__('Спойлери')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Додати коментар')),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('Редагувати')),
                DeleteAction::make()
                    ->label(__('Видалити')),
            ]);
    }
}
