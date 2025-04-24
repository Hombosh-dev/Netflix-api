<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TariffResource\Pages;
use App\Filament\Resources\TariffResource\RelationManagers;
use App\Models\Tariff;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TariffResource extends Resource
{
    protected static ?string $model = Tariff::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Фінанси';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Тарифи');
    }

    public static function getModelLabel(): string
    {
        return __('Тариф');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Тарифи');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Тариф')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Назва'))
                                            ->required()
                                            ->maxLength(128)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set) {
                                                if (!$state) {
                                                    return;
                                                }

                                                // Завжди оновлюємо slug при редагуванні назви
                                                $set('slug', Tariff::generateSlug($state));
                                                $set('meta_title', $state . ' | Netflix');
                                            }),

                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->maxLength(128)
                                            ->unique(Tariff::class, 'slug', ignoreRecord: true)
                                            ->helperText(__('Унікальний ідентифікатор для URL')),
                                    ]),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->required()
                                    ->rows(5)
                                    ->placeholder(__('Детальний опис тарифу'))
                                    ->columnSpanFull(),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('price')
                                            ->label(__('Ціна'))
                                            ->required()
                                            ->numeric()
                                            ->step(0.01)
                                            ->prefix('₴')
                                            ->helperText(__('Ціна тарифу в гривнях')),

                                        Select::make('currency')
                                            ->label(__('Валюта'))
                                            ->options([
                                                'UAH' => __('Гривня (UAH)'),
                                                'USD' => __('Долар (USD)'),
                                                'EUR' => __('Євро (EUR)'),
                                            ])
                                            ->default('UAH')
                                            ->required()
                                            ->helperText(__('Валюта тарифу')),

                                        TextInput::make('duration_days')
                                            ->label(__('Тривалість (днів)'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(30)
                                            ->helperText(__('Тривалість тарифу в днях')),
                                    ]),

                                TagsInput::make('features')
                                    ->label(__('Переваги'))
                                    ->placeholder(__('Додайте переваги тарифу'))
                                    ->helperText(__('Переваги тарифу, наприклад: "HD якість", "4K якість", "Без реклами"'))
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label(__('Активний'))
                                    ->helperText(__('Активні тарифи відображаються на сайті'))
                                    ->default(true)
                                    ->required(),
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

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('price')
                    ->label(__('Ціна'))
                    ->money('UAH')
                    ->sortable(),

                TextColumn::make('duration_days')
                    ->label(__('Тривалість'))
                    ->formatStateUsing(fn (int $state) => $state . ' ' . __('днів'))
                    ->sortable(),

                TextColumn::make('features')
                    ->label(__('Переваги'))
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return '';
                        return is_array($state) || $state instanceof \Illuminate\Support\Collection
                            ? collect($state)->join(', ')
                            : $state;
                    })
                    ->limit(50)
                    ->tooltip(function ($record) {
                        if (empty($record->features)) return '';
                        return is_array($record->features) || $record->features instanceof \Illuminate\Support\Collection
                            ? collect($record->features)->join(', ')
                            : $record->features;
                    })
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('Активний'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('user_subscriptions_count')
                    ->label(__('Підписки'))
                    ->counts('userSubscriptions')
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
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('Оновлено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Статус'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Активні'))
                    ->falseLabel(__('Неактивні'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('is_active', true),
                        false: fn (Builder $query): Builder => $query->where('is_active', false),
                        blank: fn (Builder $query): Builder => $query,
                    )
                    ->indicator(__('Статус')),

                Filter::make('price_range')
                    ->label(__('Діапазон цін'))
                    ->form([
                        TextInput::make('min_price')
                            ->label(__('Мінімальна ціна'))
                            ->numeric()
                            ->prefix('₴'),
                        TextInput::make('max_price')
                            ->label(__('Максимальна ціна'))
                            ->numeric()
                            ->prefix('₴'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                Filter::make('duration_range')
                    ->label(__('Тривалість'))
                    ->form([
                        Select::make('duration')
                            ->label(__('Тривалість'))
                            ->options([
                                '7' => __('Тиждень (7 днів)'),
                                '30' => __('Місяць (30 днів)'),
                                '90' => __('Квартал (90 днів)'),
                                '180' => __('Півроку (180 днів)'),
                                '365' => __('Рік (365 днів)'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['duration'],
                                fn (Builder $query, $duration): Builder => $query->where('duration_days', $duration),
                            );
                    }),

                Filter::make('created_at')
                    ->label(__('Дата створення'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('Від'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('created_until')
                            ->label(__('До'))
                            ->displayFormat('d.m.Y'),
                    ])
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
            ->defaultSort('created_at', 'desc')
            ->groups([
                'is_active',
                'currency',
            ]);
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\UserSubscriptionsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTariffs::route('/'),
            'create' => Pages\CreateTariff::route('/create'),
            'view' => Pages\ViewTariff::route('/{record}'),
            'edit' => Pages\EditTariff::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['userSubscriptions']);
    }
}