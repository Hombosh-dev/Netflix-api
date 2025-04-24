<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentLikeResource\Pages;
use App\Models\CommentLike;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentLikeResource extends Resource
{
    protected static ?string $model = CommentLike::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('Лайки коментарів');
    }

    public static function getModelLabel(): string
    {
        return __('Лайк коментаря');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Лайки коментарів');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('comment_id')
                    ->label(__('Коментар'))
                    ->relationship('comment', 'body')
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
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->user->email ?? 'unknown'),

                TextColumn::make('comment.body')
                    ->label(__('Коментар'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->comment?->body ?? 'Коментар видалено')
                    ->searchable(),
                
                TextColumn::make('comment.commentable_type')
                    ->label(__('Тип контенту'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'App\\Models\\Movie' => __('Фільм'),
                        'App\\Models\\Episode' => __('Епізод'),
                        'App\\Models\\Selection' => __('Підбірка'),
                        default => __('Невідомий контент'),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'App\\Models\\Movie' => 'success',
                        'App\\Models\\Episode' => 'info',
                        'App\\Models\\Selection' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('is_liked')
                    ->label(__('Тип реакції'))
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state
                        ? __('Лайк')
                        : __('Дизлайк')
                    )
                    ->icon(fn(bool $state): string => $state
                        ? 'heroicon-o-hand-thumb-up'
                        : 'heroicon-o-hand-thumb-down'
                    )
                    ->color(fn(bool $state): string => $state
                        ? 'success'
                        : 'danger'
                    ),

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
                TernaryFilter::make('is_liked')
                    ->label(__('Тип реакції'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Лайки'))
                    ->falseLabel(__('Дизлайки'))
                    ->indicator(__('Тип реакції'))
                    ->queries(
                        true: fn(Builder $query) => $query->where('is_liked', true),
                        false: fn(Builder $query) => $query->where('is_liked', false),
                        blank: fn(Builder $query) => $query,
                    ),

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

                SelectFilter::make('comment_id')
                    ->label(__('Коментар'))
                    ->relationship('comment', 'body')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Коментар')),

                SelectFilter::make('commentable_type')
                    ->label(__('Тип контенту'))
                    ->options([
                        'App\\Models\\Movie' => __('Фільм'),
                        'App\\Models\\Episode' => __('Епізод'),
                        'App\\Models\\Selection' => __('Підбірка'),
                    ])
                    ->multiple()
                    ->indicator(__('Тип контенту'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['values'],
                            fn(Builder $query, $types): Builder => $query->whereHas(
                                'comment',
                                fn(Builder $query) => $query->whereIn('commentable_type', $types)
                            )
                        );
                    }),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommentLikes::route('/'),
            'create' => Pages\CreateCommentLike::route('/create'),
            'edit' => Pages\EditCommentLike::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'comment']);
    }
}
