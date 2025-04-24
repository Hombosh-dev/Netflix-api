<?php

namespace App\Filament\Resources\TariffResource\RelationManagers;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Платежі');
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
                    
                TextInput::make('amount')
                    ->label(__('Сума'))
                    ->required()
                    ->numeric()
                    ->prefix('₴'),
                    
                Select::make('status')
                    ->label(__('Статус'))
                    ->options(PaymentStatus::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Користувач'))
                    ->description(fn ($record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('amount')
                    ->label(__('Сума'))
                    ->money('UAH')
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label(__('Статус'))
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match($state) {
                        PaymentStatus::SUCCESS => 'success',
                        PaymentStatus::PENDING => 'warning',
                        PaymentStatus::FAILED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label(__('Дата платежу'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Статус'))
                    ->options(PaymentStatus::class)
                    ->multiple()
                    ->indicator(__('Статус')),
                    
                Filter::make('amount_range')
                    ->label(__('Діапазон суми'))
                    ->form([
                        TextInput::make('min_amount')
                            ->label(__('Мінімальна сума'))
                            ->numeric()
                            ->prefix('₴'),
                        TextInput::make('max_amount')
                            ->label(__('Максимальна сума'))
                            ->numeric()
                            ->prefix('₴'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
                    
                Filter::make('created_at')
                    ->label(__('Дата платежу'))
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
                ViewAction::make()
                    ->label(__('Переглянути')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
