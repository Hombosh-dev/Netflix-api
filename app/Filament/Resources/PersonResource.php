<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Персони');
    }

    public static function getModelLabel(): string
    {
        return __('Персона');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Персони');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Персона')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Ім\'я'))
                                            ->required()
                                            ->maxLength(128)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set) {
                                                if (!$state) {
                                                    return;
                                                }

                                                // Завжди оновлюємо slug при редагуванні імені
                                                $set('slug', Person::generateSlug($state));
                                            }),

                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->maxLength(128)
                                            ->unique(Person::class, 'slug', ignoreRecord: true)
                                            ->helperText(__('Унікальний ідентифікатор для URL')),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('original_name')
                                            ->label(__('Оригінальне ім\'я'))
                                            ->maxLength(128)
                                            ->placeholder(__('Ім\'я мовою оригіналу')),

                                        Select::make('type')
                                            ->label(__('Тип'))
                                            ->options(PersonType::class)
                                            ->enum(PersonType::class)
                                            ->required()
                                            ->helperText(__('Роль у кіноіндустрії')),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('gender')
                                            ->label(__('Стать'))
                                            ->options(Gender::class)
                                            ->enum(Gender::class)
                                            ->nullable(),

                                        DatePicker::make('birthday')
                                            ->label(__('Дата народження'))
                                            ->maxDate(now())
                                            ->nullable()
                                            ->displayFormat('d.m.Y'),
                                    ]),

                                TextInput::make('birthplace')
                                    ->label(__('Місце народження'))
                                    ->maxLength(248)
                                    ->placeholder(__('Місто, країна')),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->maxLength(512)
                                    ->rows(5)
                                    ->placeholder(__('Коротка біографія персони'))
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Медіа'))
                            ->schema([
                                Section::make(__('Зображення'))
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label(__('Фото'))
                                            ->image()
                                            ->directory('people')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('300')
                                            ->imageResizeTargetHeight('300')
                                            ->helperText(__('Рекомендований розмір: 300x300 пікселів'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('SEO'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->placeholder('{name} | Netflix')
                                    ->maxLength(128)
                                    ->helperText(__('Заголовок для пошукових систем')),

                                Textarea::make('meta_description')
                                    ->label(__('Meta Description'))
                                    ->maxLength(376)
                                    ->rows(3)
                                    ->placeholder(__('Опис для пошукових систем'))
                                    ->helperText(__('Оптимальна довжина: 150-160 символів')),

                                FileUpload::make('meta_image')
                                    ->label(__('Meta Image'))
                                    ->image()
                                    ->directory('seo')
                                    ->helperText(__('Зображення для соціальних мереж. Рекомендований розмір: 1200x630 пікселів'))
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

                ImageColumn::make('image')
                    ->label(__('Фото'))
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/default-avatar.png'))
                    ->size(60),

                TextColumn::make('name')
                    ->label(__('Ім\'я'))
                    ->description(fn(?Person $record) => $record?->original_name)
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('type')
                    ->label(__('Тип'))
                    ->badge()
                    ->color(fn (PersonType $state): string => match($state) {
                        PersonType::ACTOR => 'success',
                        PersonType::DIRECTOR => 'warning',
                        PersonType::WRITER => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('gender')
                    ->label(__('Стать'))
                    ->badge()
                    ->color(fn (Gender $state): string => match($state) {
                        Gender::MALE => 'info',
                        Gender::FEMALE => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('birthday')
                    ->label(__('Дата народження'))
                    ->date('d.m.Y')
                    ->description(fn(?Person $record
                    ) => $record?->birthday && $record->age > 0 ? __('Вік').': '.$record->age.' '.__('років') : null)
                    ->sortable(),

                TextColumn::make('birthplace')
                    ->label(__('Місце народження'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('movies_count')
                    ->label(__('Фільми'))
                    ->counts('movies')
                    ->sortable()
                    ->color(fn (int $state): string => match(true) {
                        $state > 10 => 'success',
                        $state > 5 => 'warning',
                        $state > 0 => 'info',
                        default => 'gray',
                    }),

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
                SelectFilter::make('type')
                    ->label(__('Тип'))
                    ->options(PersonType::class)
                    ->multiple()
                    ->indicator(__('Тип')),

                SelectFilter::make('gender')
                    ->label(__('Стать'))
                    ->options(Gender::class)
                    ->multiple()
                    ->indicator(__('Стать')),

                Filter::make('birthday')
                    ->label(__('Дата народження'))
                    ->form([
                        DatePicker::make('born_from')
                            ->label(__('Від'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('born_until')
                            ->label(__('До'))
                            ->displayFormat('d.m.Y'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['born_from'] ?? null) {
                            $indicators[] = __('Від').': '.$data['born_from'];
                        }

                        if ($data['born_until'] ?? null) {
                            $indicators[] = __('До').': '.$data['born_until'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['born_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('birthday', '>=', $date),
                            )
                            ->when(
                                $data['born_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('birthday', '<=', $date),
                            );
                    }),

                TernaryFilter::make('has_movies')
                    ->label(__('Наявність фільмів'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('З фільмами'))
                    ->falseLabel(__('Без фільмів'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->withMovies(),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('movies'),
                        blank: fn (Builder $query): Builder => $query,
                    )
                    ->indicator(__('Фільми')),

                Filter::make('age')
                    ->label(__('Вік'))
                    ->form([
                        TextInput::make('min_age')
                            ->label(__('Мінімальний вік'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120),
                        TextInput::make('max_age')
                            ->label(__('Максимальний вік'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(120),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['min_age'] ?? null) {
                            $indicators[] = __('Від').': '.$data['min_age'].' '.__('років');
                        }

                        if ($data['max_age'] ?? null) {
                            $indicators[] = __('До').': '.$data['max_age'].' '.__('років');
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_age'],
                                function (Builder $query, $minAge): Builder {
                                    $maxDate = now()->subYears($minAge)->format('Y-m-d');
                                    return $query->whereDate('birthday', '<=', $maxDate);
                                },
                            )
                            ->when(
                                $data['max_age'],
                                function (Builder $query, $maxAge): Builder {
                                    $minDate = now()->subYears($maxAge + 1)->format('Y-m-d');
                                    return $query->whereDate('birthday', '>=', $minDate);
                                },
                            );
                    }),

                Filter::make('recently_added')
                    ->label(__('Нещодавно додані'))
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
                Action::make('view_movies')
                    ->label(__('Переглянути фільми'))
                    ->icon('heroicon-o-film')
                    ->url(fn(?Person $record) => $record ? route('filament.admin.resources.movies.index',
                        ['tableFilters[persons][value]' => $record->id]) : '#')
                    ->openUrlInNewTab(),

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
                'type',
                'gender',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MoviesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'view' => Pages\ViewPerson::route('/{record}'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('movies');
    }
}
