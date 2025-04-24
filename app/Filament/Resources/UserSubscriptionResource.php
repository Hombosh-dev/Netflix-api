<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSubscriptionResource\Pages;
use App\Filament\Resources\UserSubscriptionResource\RelationManagers;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Фінанси';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Підписки');
    }

    public static function getModelLabel(): string
    {
        return __('Підписка');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Підписки');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('Підписка'))
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

                                        Select::make('tariff_id')
                                            ->label(__('Тариф'))
                                            ->relationship('tariff', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->label(__('Дата початку'))
                                            ->required()
                                            ->displayFormat('d.m.Y'),

                                        DatePicker::make('end_date')
                                            ->label(__('Дата закінчення'))
                                            ->required()
                                            ->displayFormat('d.m.Y')
                                            ->after('start_date'),
                                    ]),
                            ]),

                        Tab::make(__('Налаштування'))
                            ->schema([
                                Section::make(__('Статус'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->label(__('Активна'))
                                                    ->default(true)
                                                    ->required(),

                                                Toggle::make('auto_renew')
                                                    ->label(__('Автопродовження'))
                                                    ->default(false)
                                                    ->required(),
                                            ]),
                                    ]),
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

                TextColumn::make('tariff.name')
                    ->label(__('Тариф'))
                    ->description(fn ($record) => $record->tariff ? 
                        $record->tariff->price . ' ' . $record->tariff->currency . ' / ' . 
                        $record->tariff->duration_days . ' ' . __('днів') : '')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('Дата початку'))
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('Дата закінчення'))
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('daysLeft')
                    ->label(__('Залишилось днів'))
                    ->getStateUsing(fn (UserSubscription $record): int => $record->daysLeft())
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state <= 0 => 'danger',
                        $state <= 3 => 'warning',
                        $state <= 7 => 'info',
                        default => 'success',
                    }),

                IconColumn::make('is_active')
                    ->label(__('Активна'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make('auto_renew')
                    ->label(__('Автопродовження'))
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('info')
                    ->falseColor('gray')
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
                SelectFilter::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator(__('Користувач')),

                SelectFilter::make('tariff_id')
                    ->label(__('Тариф'))
                    ->relationship('tariff', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator(__('Тариф')),

                TernaryFilter::make('is_active')
                    ->label(__('Статус'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Активні'))
                    ->falseLabel(__('Неактивні'))
                    ->indicator(__('Статус')),

                TernaryFilter::make('auto_renew')
                    ->label(__('Автопродовження'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('З автопродовженням'))
                    ->falseLabel(__('Без автопродовження'))
                    ->indicator(__('Автопродовження')),

                Filter::make('expiring_soon')
                    ->label(__('Закінчуються скоро'))
                    ->form([
                        DatePicker::make('days')
                            ->label(__('Протягом днів'))
                            ->default(now()->addDays(7)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['days'],
                            function (Builder $query, $date) {
                                return $query->where('is_active', true)
                                    ->where('end_date', '<=', $date)
                                    ->where('end_date', '>=', now());
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['days'] ?? null) {
                            return __('Закінчуються до') . ' ' . Carbon::parse($data['days'])->format('d.m.Y');
                        }

                        return null;
                    }),

                Filter::make('date_range')
                    ->label(__('Період підписки'))
                    ->form([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_from')
                                    ->label(__('Початок від')),
                                DatePicker::make('start_until')
                                    ->label(__('Початок до')),
                                DatePicker::make('end_from')
                                    ->label(__('Кінець від')),
                                DatePicker::make('end_until')
                                    ->label(__('Кінець до')),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            )
                            ->when(
                                $data['end_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '>=', $date),
                            )
                            ->when(
                                $data['end_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['start_from'] ?? null) {
                            $indicators[] = __('Початок від') . ' ' . Carbon::parse($data['start_from'])->format('d.m.Y');
                        }

                        if ($data['start_until'] ?? null) {
                            $indicators[] = __('Початок до') . ' ' . Carbon::parse($data['start_until'])->format('d.m.Y');
                        }

                        if ($data['end_from'] ?? null) {
                            $indicators[] = __('Кінець від') . ' ' . Carbon::parse($data['end_from'])->format('d.m.Y');
                        }

                        if ($data['end_until'] ?? null) {
                            $indicators[] = __('Кінець до') . ' ' . Carbon::parse($data['end_until'])->format('d.m.Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('extend')
                    ->label(__('Продовжити'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->form([
                        Select::make('tariff_id')
                            ->label(__('Тариф'))
                            ->options(Tariff::query()->pluck('name', 'id'))
                            ->required(),
                        Toggle::make('auto_renew')
                            ->label(__('Автопродовження'))
                            ->default(false),
                    ])
                    ->action(function (UserSubscription $record, array $data): void {
                        $tariff = Tariff::find($data['tariff_id']);
                        $startDate = $record->end_date > now() ? $record->end_date : now();
                        
                        $record->update([
                            'tariff_id' => $data['tariff_id'],
                            'start_date' => $startDate,
                            'end_date' => Carbon::parse($startDate)->addDays($tariff->duration_days),
                            'is_active' => true,
                            'auto_renew' => $data['auto_renew'],
                        ]);
                    })
                    ->visible(fn (UserSubscription $record): bool => $record->is_active),

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
                'tariff.name',
                'is_active',
                'auto_renew',
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
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'view' => Pages\ViewUserSubscription::route('/{record}'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'tariff']);
    }
}
