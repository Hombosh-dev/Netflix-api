<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Модерація';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Коментарі');
    }

    public static function getModelLabel(): string
    {
        return __('Коментар');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Коментарі');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Основна інформація'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('Користувач'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('commentable_type')
                                    ->label(__('Тип контенту'))
                                    ->options([
                                        Movie::class => __('Фільм'),
                                        Episode::class => __('Епізод'),
                                        Selection::class => __('Підбірка'),
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (callable $set) => $set('commentable_id', null)),
                            ]),

                        Select::make('commentable_id')
                            ->label(__('Об\'єкт'))
                            ->options(function (callable $get) {
                                $type = $get('commentable_type');
                                if (!$type) {
                                    return [];
                                }

                                return match ($type) {
                                    Movie::class => Movie::pluck('name', 'id')->toArray(),
                                    Episode::class => Episode::pluck('name', 'id')->toArray(),
                                    Selection::class => Selection::pluck('name', 'id')->toArray(),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('parent_id', null)),

                        Select::make('parent_id')
                            ->label(__('Відповідь на'))
                            ->options(function (callable $get) {
                                $type = $get('commentable_type');
                                $id = $get('commentable_id');

                                if (!$type || !$id) {
                                    return [];
                                }

                                return Comment::where('commentable_type', $type)
                                    ->where('commentable_id', $id)
                                    ->whereNull('parent_id')
                                    ->pluck('body', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->placeholder(__('Це основний коментар')),

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
                    ])
                    ->columns(1),

                Section::make(__('Інформація про контент'))
                    ->schema([
                        Placeholder::make('commentable_type_label')
                            ->label(__('Тип контенту'))
                            ->content(fn(?Comment $record
                            ): string => $record ? $record->getTranslatedTypeAttribute() : '-'),

                        Placeholder::make('commentable_title')
                            ->label(__('Назва контенту'))
                            ->content(function (?Comment $record): string {
                                if (!$record) {
                                    return '-';
                                }

                                return match ($record->commentable_type) {
                                    Movie::class => $record->commentable?->name ?? '-',
                                    Episode::class => $record->commentable?->name ?? '-',
                                    Selection::class => $record->commentable?->name ?? '-',
                                    default => '-',
                                };
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn(?Comment $record): bool => $record !== null),

                Section::make(__('Статистика'))
                    ->schema([
                        Placeholder::make('likes_count')
                            ->label(__('Кількість лайків'))
                            ->content(fn(?Comment $record
                            ): string => $record ? (string) $record->likes()->where('is_liked', true)->count() : '0'),

                        Placeholder::make('dislikes_count')
                            ->label(__('Кількість дизлайків'))
                            ->content(fn(?Comment $record
                            ): string => $record ? (string) $record->likes()->where('is_liked', false)->count() : '0'),

                        Placeholder::make('reports_count')
                            ->label(__('Кількість скарг'))
                            ->content(fn(?Comment $record
                            ): string => $record ? (string) $record->reports()->count() : '0'),

                        Placeholder::make('replies_count')
                            ->label(__('Кількість відповідей'))
                            ->content(fn(?Comment $record
                            ): string => $record ? (string) $record->children()->count() : '0'),
                    ])
                    ->columns(2)
                    ->visible(fn(?Comment $record): bool => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label(__('Користувач'))
                    ->description(fn($record) => $record->user->email ?? 'unknown')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->label(__('Текст'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->body)
                    ->searchable(),

                TextColumn::make('commentable_type')
                    ->label(__('Тип контенту'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Movie::class => __('Фільм'),
                        Episode::class => __('Епізод'),
                        Selection::class => __('Підбірка'),
                        default => __('Невідомий контент'),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        Movie::class => 'success',
                        Episode::class => 'info',
                        Selection::class => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('parent.body')
                    ->label(__('Відповідь на'))
                    ->limit(30)
                    ->tooltip(fn($record) => $record->parent?->body)
                    ->placeholder(__('Основний коментар'))
                    ->toggleable(),

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
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label(__('Оновлено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Filter::make('created_at')
                    ->label(__('Дата створення'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('Від')),
                        DatePicker::make('created_until')
                            ->label(__('До')),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('Від').': '.$data['created_from'];
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('До').': '.$data['created_until'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                SelectFilter::make('commentable_type')
                    ->label(__('Тип контенту'))
                    ->options([
                        Movie::class => __('Фільм'),
                        Episode::class => __('Епізод'),
                        Selection::class => __('Підбірка'),
                    ])
                    ->multiple()
                    ->indicator(__('Тип контенту')),

                TernaryFilter::make('has_parent')
                    ->label(__('Тип коментаря'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Відповіді'))
                    ->falseLabel(__('Основні'))
                    ->indicator(__('Тип коментаря'))
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('parent_id'),
                        false: fn(Builder $query) => $query->whereNull('parent_id'),
                        blank: fn(Builder $query) => $query,
                    ),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LikesRelationManager::class,
            RelationManagers\ReportsRelationManager::class,
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'view' => Pages\ViewComment::route('/{record}'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'parent']);
    }
}
