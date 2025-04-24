<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Фінанси';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Платежі');
    }

    public static function getModelLabel(): string
    {
        return __('Платіж');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Платежі');
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

                                Select::make('tariff_id')
                                    ->label(__('Тариф'))
                                    ->relationship('tariff', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
                                    ->label(__('Сума'))
                                    ->required()
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0),

                                TextInput::make('currency')
                                    ->label(__('Валюта'))
                                    ->required()
                                    ->maxLength(3)
                                    ->default('UAH'),

                                Select::make('status')
                                    ->label(__('Статус'))
                                    ->options(PaymentStatus::class)
                                    ->enum(PaymentStatus::class)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('payment_method')
                                    ->label(__('Спосіб оплати'))
                                    ->required()
                                    ->maxLength(50)
                                    ->default('LiqPay'),

                                TextInput::make('transaction_id')
                                    ->label(__('ID транзакції'))
                                    ->required()
                                    ->maxLength(128)
                                    ->default(fn() => (string) Str::uuid()),
                            ]),
                    ]),

                Section::make(__('Дані LiqPay'))
                    ->schema([
                        Textarea::make('liqpay_data')
                            ->label(__('Дані LiqPay'))
                            ->required()
                            ->default('{}')
                            ->rows(10)
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($state, $component) {
                                // Якщо дані в JSON форматі, перетворюємо їх на рядок
                                if (is_array($state) || is_object($state)) {
                                    $component->state(json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Перетворюємо рядок на JSON перед збереженням
                                if (is_string($state)) {
                                    try {
                                        return json_decode($state, true) ?: $state;
                                    } catch (Exception $e) {
                                        return $state;
                                    }
                                }
                                return $state;
                            }),
                    ])
                    ->collapsible(),
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
                    ->description(fn($record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tariff.name')
                    ->label(__('Тариф'))
                    ->description(fn($record
                    ) => $record->tariff ? "{$record->tariff->price} {$record->tariff->currency}" : null)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('Сума'))
                    ->money(fn($record) => $record->currency)
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Статус'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('Спосіб оплати'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('transaction_id')
                    ->label(__('ID транзакції'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Дата'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Статус'))
                    ->options(PaymentStatus::class)
                    ->multiple()
                    ->indicator(__('Статус')),

                SelectFilter::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Користувач')),

                SelectFilter::make('tariff_id')
                    ->label(__('Тариф'))
                    ->relationship('tariff', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Тариф')),

                Filter::make('amount')
                    ->label(__('Сума'))
                    ->form([
                        TextInput::make('min_amount')
                            ->label(__('Мінімальна сума'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('max_amount')
                            ->label(__('Максимальна сума'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['min_amount'] ?? null) {
                            $indicators[] = __('Від').': '.$data['min_amount'];
                        }

                        if ($data['max_amount'] ?? null) {
                            $indicators[] = __('До').': '.$data['max_amount'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),

                Filter::make('created_at')
                    ->label(__('Дата'))
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

                SelectFilter::make('payment_method')
                    ->label(__('Спосіб оплати'))
                    ->options([
                        'LiqPay' => 'LiqPay',
                        'card' => __('Картка'),
                        'privat24' => 'Приват24',
                        'cash' => __('Готівка'),
                        'invoice' => __('Рахунок'),
                    ])
                    ->multiple()
                    ->indicator(__('Спосіб оплати')),
            ])
            ->actions([
                Action::make('view_user')
                    ->label(__('Користувач'))
                    ->icon('heroicon-o-user')
                    ->url(fn(Payment $record) => route('filament.admin.resources.users.edit', $record->user))
                    ->openUrlInNewTab()
                    ->visible(fn(?Payment $record) => $record?->user_id !== null),

                Action::make('view_tariff')
                    ->label(__('Тариф'))
                    ->icon('heroicon-o-ticket')
                    ->url(fn(Payment $record) => route('filament.admin.resources.tariffs.edit', $record->tariff))
                    ->openUrlInNewTab()
                    ->visible(fn(?Payment $record) => $record?->tariff_id !== null),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'tariff']);
    }
}
