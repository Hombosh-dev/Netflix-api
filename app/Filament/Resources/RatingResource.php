<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Models\Rating;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('Рейтинги');
    }

    public static function getModelLabel(): string
    {
        return __('Рейтинг');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Рейтинги');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Рейтинг')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('Користувач'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Select::make('movie_id')
                                            ->label(__('Фільм'))
                                            ->relationship('movie', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),

                                TextInput::make('number')
                                    ->label(__('Оцінка'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->step(1)
                                    ->helperText(__('Оцінка від 1 до 10')),

                                Textarea::make('review')
                                    ->label(__('Відгук'))
                                    ->nullable()
                                    ->rows(5)
                                    ->placeholder(__('Детальний відгук про фільм'))
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                    ->description(fn ($record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('updated_at')
                    ->label(__('Оновлено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Користувач')),

                SelectFilter::make('movie_id')
                    ->label(__('Фільм'))
                    ->relationship('movie', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Фільм')),

                Filter::make('rating_range')
                    ->label(__('Діапазон оцінок'))
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
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['min_rating'] ?? null) {
                            $indicators[] = __('Від').': '.$data['min_rating'];
                        }

                        if ($data['max_rating'] ?? null) {
                            $indicators[] = __('До').': '.$data['max_rating'];
                        }

                        return $indicators;
                    })
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

                TernaryFilter::make('high_rating')
                    ->label(__('Висока оцінка'))
                    ->placeholder(__('Всі оцінки'))
                    ->trueLabel(__('Тільки високі (8-10)'))
                    ->falseLabel(__('Тільки низькі (1-4)'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->highRatings(8),
                        false: fn (Builder $query): Builder => $query->lowRatings(4),
                        blank: fn (Builder $query): Builder => $query,
                    )
                    ->indicator(__('Рівень оцінки')),

                TernaryFilter::make('has_review')
                    ->label(__('Наявність відгуку'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('З відгуком'))
                    ->falseLabel(__('Без відгуку'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->withReviews(),
                        false: fn (Builder $query): Builder => $query->whereNull('review')->orWhere('review', ''),
                        blank: fn (Builder $query): Builder => $query,
                    )
                    ->indicator(__('Відгук')),

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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('recent_ratings')
                    ->label(__('Нещодавні оцінки'))
                    ->form([
                        TextInput::make('days')
                            ->label(__('Кількість днів'))
                            ->numeric()
                            ->default(7),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['days'] ?? null) {
                            return __('За останні').' '.$data['days'].' '.__('днів');
                        }

                        return null;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['days'],
                            fn (Builder $query, $days): Builder => $query->where('created_at', '>=', Carbon::now()->subDays((int) $days)),
                        );
                    }),
            ])
            ->actions([
                Action::make('view_user')
                    ->label(__('Переглянути користувача'))
                    ->icon('heroicon-o-user')
                    ->url(fn (Rating $record) => route('filament.admin.resources.users.edit', $record->user))
                    ->openUrlInNewTab()
                    ->visible(fn (?Rating $record) => $record?->user_id !== null),

                Action::make('view_movie')
                    ->label(__('Переглянути фільм'))
                    ->icon('heroicon-o-film')
                    ->url(fn (Rating $record) => route('filament.admin.resources.movies.edit', $record->movie))
                    ->openUrlInNewTab()
                    ->visible(fn (?Rating $record) => $record?->movie_id !== null),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                'user.name',
                'movie.name',
            ]);
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
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'view' => Pages\ViewRating::route('/{record}'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'movie']);
    }
}
