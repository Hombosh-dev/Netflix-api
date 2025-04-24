<?php

namespace App\Filament\Resources;

use App\Enums\Kind;
use App\Enums\Status;
use App\Filament\Resources\MovieResource\Pages;
use App\Filament\Resources\MovieResource\RelationManagers;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Studio;
use App\Models\Tag;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MovieResource extends Resource
{
    protected static ?string $model = Movie::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Фільми');
    }

    public static function getModelLabel(): string
    {
        return __('Фільм');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Фільми');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Фільм')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Назва'))
                                            ->required()
                                            ->maxLength(248)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set) {
                                                if (!$state) {
                                                    return;
                                                }

                                                // Завжди оновлюємо slug при редагуванні назви
                                                $set('slug', Movie::generateSlug($state));
                                            }),

                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->maxLength(128)
                                            ->unique(Movie::class, 'slug', ignoreRecord: true),
                                    ]),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('kind')
                                            ->label(__('Тип'))
                                            ->options(Kind::class)
                                            ->enum(Kind::class)
                                            ->required(),

                                        Select::make('status')
                                            ->label(__('Статус'))
                                            ->options(Status::class)
                                            ->enum(Status::class)
                                            ->required(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('studio_id')
                                            ->label(__('Студія'))
                                            ->relationship('studio', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('Назва'))
                                                    ->required()
                                                    ->maxLength(128),
                                                TextInput::make('slug')
                                                    ->label(__('Slug'))
                                                    ->required()
                                                    ->maxLength(128)
                                                    ->unique(Studio::class, 'slug', ignoreRecord: true),
                                                Textarea::make('description')
                                                    ->label(__('Опис'))
                                                    ->rows(3),
                                            ])
                                            ->required(),

                                        TagsInput::make('countries')
                                            ->label(__('Країни'))
                                            ->placeholder(__('Додайте країну'))
                                            ->required(),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('duration')
                                            ->label(__('Тривалість (хв)'))
                                            ->numeric()
                                            ->minValue(1),

                                        TextInput::make('episodes_count')
                                            ->label(__('Кількість епізодів'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->visible(fn(Get $get): bool => in_array($get('kind'), [
                                                Kind::TV_SERIES->value,
                                                Kind::ANIMATED_SERIES->value
                                            ])),

                                        TextInput::make('imdb_score')
                                            ->label(__('IMDb рейтинг'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->step(0.1),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        DatePicker::make('first_air_date')
                                            ->label(__('Дата виходу'))
                                            ->default(fn() => Carbon::now()),

                                        DatePicker::make('last_air_date')
                                            ->label(__('Дата завершення'))
                                            ->visible(fn(Get $get): bool => in_array($get('kind'), [
                                                Kind::TV_SERIES->value,
                                                Kind::ANIMATED_SERIES->value
                                            ])),

                                        Toggle::make('is_published')
                                            ->label(__('Опубліковано'))
                                            ->default(true)
                                            ->required(),
                                    ]),

                                TagsInput::make('aliases')
                                    ->label(__('Альтернативні назви'))
                                    ->placeholder(__('Додайте альтернативну назву'))
                                    ->columnSpanFull(),
                            ]),
                        
                        Tab::make(__('Медіа'))
                            ->schema([
                                Section::make(__('Зображення'))
                                    ->schema([
                                        FileUpload::make('image_name')
                                            ->label(__('Заголовок зображення'))
                                            ->image()
                                            ->required()
                                            ->directory('movies')
                                            ->columnSpanFull(),

                                        FileUpload::make('poster')
                                            ->label(__('Постер'))
                                            ->image()
                                            ->directory('posters')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Трейлери та тизери'))
                                    ->schema([
                                        Repeater::make('attachments')
                                            ->label(__('Вкладення'))
                                            ->schema([
                                                Select::make('type')
                                                    ->label(__('Тип'))
                                                    ->options([
                                                        'trailer' => __('Трейлер'),
                                                        'teaser' => __('Тизер'),
                                                        'clip' => __('Кліп'),
                                                        'behind_the_scenes' => __('За кадром'),
                                                    ])
                                                    ->required(),

                                                TextInput::make('url')
                                                    ->label(__('URL'))
                                                    ->url()
                                                    ->required(),

                                                TextInput::make('title')
                                                    ->label(__('Заголовок'))
                                                    ->required(),

                                                TextInput::make('duration')
                                                    ->label(__('Тривалість (сек)'))
                                                    ->numeric()
                                                    ->minValue(1),
                                            ])
                                            ->defaultItems(1)
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('Зв\'язки'))
                            ->schema([
                                Section::make(__('Теги'))
                                    ->schema([
                                        Select::make('tags')
                                            ->label(__('Теги'))
                                            ->relationship('tags', 'name',
                                                fn(Builder $query) => $query->select(['id', 'name']))
                                            ->multiple()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('Назва'))
                                                    ->required()
                                                    ->maxLength(128),
                                                TextInput::make('slug')
                                                    ->label(__('Slug'))
                                                    ->required()
                                                    ->maxLength(128)
                                                    ->unique(Tag::class, 'slug', ignoreRecord: true),
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Актори та персонажі'))
                                    ->schema([
                                        Select::make('persons')
                                            ->label(__('Актори'))
                                            ->relationship('persons', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('Ім\'я'))
                                                    ->required()
                                                    ->maxLength(128),
                                                TextInput::make('slug')
                                                    ->label(__('Slug'))
                                                    ->required()
                                                    ->maxLength(128)
                                                    ->unique(Person::class, 'slug', ignoreRecord: true),
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Пов\'язані фільми'))
                                    ->schema([
                                        Repeater::make('related')
                                            ->label(__('Пов\'язані фільми'))
                                            ->schema([
                                                Select::make('movie_id')
                                                    ->label(__('Фільм'))
                                                    ->options(function (?Movie $record) {
                                                        if (!$record) {
                                                            return Movie::all()->pluck('name', 'id');
                                                        }

                                                        return Movie::where('id', '!=', $record->id)
                                                            ->get()
                                                            ->pluck('name', 'id');
                                                    })
                                                    ->searchable()
                                                    ->required(),

                                                Select::make('type')
                                                    ->label(__('Тип зв\'язку'))
                                                    ->options([
                                                        'sequel' => __('Сиквел'),
                                                        'prequel' => __('Приквел'),
                                                        'remake' => __('Ремейк'),
                                                        'spin_off' => __('Спін-офф'),
                                                        'adaptation' => __('Адаптація'),
                                                    ])
                                                    ->required(),
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Схожі фільми'))
                                    ->schema([
                                        Select::make('similars')
                                            ->label(__('Схожі фільми'))
                                            ->options(function (?Movie $record) {
                                                if (!$record) {
                                                    return Movie::all()->pluck('name', 'id');
                                                }

                                                return Movie::where('id', '!=', $record->id)
                                                    ->get()
                                                    ->pluck('name', 'id');
                                            })
                                            ->multiple()
                                            ->searchable()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('API'))
                            ->schema([
                                Section::make(__('API джерела'))
                                    ->schema([
                                        Repeater::make('api_sources')
                                            ->label(__('API джерела'))
                                            ->schema([
                                                Select::make('source')
                                                    ->label(__('Джерело'))
                                                    ->options([
                                                        'imdb' => 'IMDb',
                                                        'tmdb' => 'TMDb',
                                                        'kinopoisk' => 'Кінопошук',
                                                        'other' => __('Інше'),
                                                    ])
                                                    ->required(),

                                                TextInput::make('id')
                                                    ->label(__('ID'))
                                                    ->required(),
                                            ])
                                            ->defaultItems(1)
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('SEO'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->placeholder('{name} ({year}) | Netflix')
                                    ->maxLength(128),

                                Textarea::make('meta_description')
                                    ->label(__('Meta Description'))
                                    ->maxLength(376)
                                    ->rows(3),

                                FileUpload::make('meta_image')
                                    ->label(__('Meta Image'))
                                    ->image()
                                    ->directory('seo')
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

                ImageColumn::make('image_name')
                    ->label(__('Зображення'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->searchable()
                    ->sortable()
                    ->description(fn(?Movie $record) => $record ? $record->fullTitle : ''),

                TextColumn::make('kind')
                    ->label(__('Тип'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Статус'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('studio.name')
                    ->label(__('Студія'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('duration')
                    ->label(__('Тривалість'))
                    ->formatStateUsing(fn(?Movie $record) => $record ? $record->formattedDuration : '')
                    ->sortable(),

                TextColumn::make('episodes_count')
                    ->label(__('Епізоди'))
                    ->numeric()
                    ->sortable()
                    ->visible(fn(?Movie $record): bool => $record ? in_array($record->kind, [
                        Kind::TV_SERIES,
                        Kind::ANIMATED_SERIES
                    ]) : false),

                TextColumn::make('imdb_score')
                    ->label(__('IMDb'))
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                TextColumn::make('first_air_date')
                    ->label(__('Дата виходу'))
                    ->date()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('Опубліковано'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Оновлено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kind')
                    ->label(__('Тип'))
                    ->options(Kind::class)
                    ->multiple()
                    ->indicator(__('Тип')),

                SelectFilter::make('status')
                    ->label(__('Статус'))
                    ->options(Status::class)
                    ->multiple()
                    ->indicator(__('Статус')),

                TernaryFilter::make('is_published')
                    ->label(__('Опубліковано'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Опубліковані'))
                    ->falseLabel(__('Неопубліковані'))
                    ->indicator(__('Опубліковано')),

                SelectFilter::make('studio_id')
                    ->label(__('Студія'))
                    ->relationship('studio', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Студія')),

                SelectFilter::make('tags')
                    ->label(__('Теги'))
                    ->relationship('tags', 'name', fn(Builder $query) => $query->select(['id', 'name']))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator(__('Теги')),

                Filter::make('countries')
                    ->label(__('Країни'))
                    ->form([
                        TagsInput::make('countries')
                            ->label(__('Країни'))
                            ->placeholder(__('Додайте країну')),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['countries'] ?? null) {
                            foreach ($data['countries'] as $country) {
                                $indicators[] = $country;
                            }
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['countries'],
                            fn(Builder $query, $countries): Builder => $query->fromCountries($countries),
                        );
                    }),

                Filter::make('release_date')
                    ->label(__('Дата виходу'))
                    ->form([
                        DatePicker::make('released_from')
                            ->label(__('Від')),
                        DatePicker::make('released_until')
                            ->label(__('До')),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['released_from'] ?? null) {
                            $indicators[] = __('Від').': '.$data['released_from'];
                        }

                        if ($data['released_until'] ?? null) {
                            $indicators[] = __('До').': '.$data['released_until'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['released_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('first_air_date', '>=', $date),
                            )
                            ->when(
                                $data['released_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('first_air_date', '<=', $date),
                            );
                    }),

                Filter::make('imdb_score')
                    ->label(__('IMDb рейтинг'))
                    ->form([
                        TextInput::make('min_score')
                            ->label(__('Мінімальний рейтинг'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.1),
                        TextInput::make('max_score')
                            ->label(__('Максимальний рейтинг'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->step(0.1),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['min_score'] ?? null) {
                            $indicators[] = __('Від').': '.$data['min_score'];
                        }

                        if ($data['max_score'] ?? null) {
                            $indicators[] = __('До').': '.$data['max_score'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_score'],
                                fn(Builder $query, $score): Builder => $query->where('imdb_score', '>=', $score),
                            )
                            ->when(
                                $data['max_score'],
                                fn(Builder $query, $score): Builder => $query->where('imdb_score', '<=', $score),
                            );
                    }),
            ])
            ->actions([
                Action::make('view_episodes')
                    ->label(__('Епізоди'))
                    ->icon('heroicon-o-film')
                    ->url(fn(?Movie $record) => $record ? route('filament.admin.resources.episodes.index',
                        ['tableFilters[movie_id][value]' => $record->id]) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn(?Movie $record): bool => $record ? in_array($record->kind, [
                        Kind::TV_SERIES,
                        Kind::ANIMATED_SERIES
                    ]) : false),

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
            RelationManagers\EpisodesRelationManager::class,
            RelationManagers\PersonsRelationManager::class,
            RelationManagers\TagsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovies::route('/'),
            'create' => Pages\CreateMovie::route('/create'),
            'view' => Pages\ViewMovie::route('/{record}'),
            'edit' => Pages\EditMovie::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['studio']);
    }
}
