<?php

namespace App\Filament\Resources\CommentResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $recordTitleAttribute = 'body';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Відповіді');
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
                    ->description(fn ($record) => '@' . ($record->user->username ?? 'unknown'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->label(__('Текст'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->body)
                    ->searchable(),

                ToggleColumn::make('is_spoiler')
                    ->label(__('Спойлер'))
                    ->onIcon('heroicon-s-exclamation-triangle')
                    ->offIcon('heroicon-s-check-circle')
                    ->onColor('danger')
                    ->offColor('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_spoiler')
                    ->label(__('Спойлер'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Зі спойлерами'))
                    ->falseLabel(__('Без спойлерів'))
                    ->indicator(__('Спойлер')),

                SelectFilter::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Користувач')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['commentable_type'] = $this->getOwnerRecord()->commentable_type;
                        $data['commentable_id'] = $this->getOwnerRecord()->commentable_id;
                        $data['parent_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
