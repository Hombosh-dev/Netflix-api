<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserSubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Підписки');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tariff_id')
                    ->label(__('Тариф'))
                    ->relationship('tariff', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                DatePicker::make('start_date')
                    ->label(__('Дата початку'))
                    ->required()
                    ->displayFormat('d.m.Y'),
                    
                DatePicker::make('end_date')
                    ->label(__('Дата закінчення'))
                    ->required()
                    ->displayFormat('d.m.Y')
                    ->after('start_date'),
                    
                Toggle::make('is_active')
                    ->label(__('Активна'))
                    ->default(true)
                    ->required(),
                    
                Toggle::make('auto_renew')
                    ->label(__('Автопродовження'))
                    ->default(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('tariff.name')
                    ->label(__('Тариф'))
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
                    
                IconColumn::make('is_active')
                    ->label(__('Активна'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                IconColumn::make('auto_renew')
                    ->label(__('Автопродовження'))
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('info')
                    ->falseColor('gray'),
                    
                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Статус активності'))
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
                    
                Filter::make('date_range')
                    ->label(__('Період підписки'))
                    ->form([
                        DatePicker::make('start_from')
                            ->label(__('Початок від'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('start_until')
                            ->label(__('Початок до'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('end_from')
                            ->label(__('Закінчення від'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('end_until')
                            ->label(__('Закінчення до'))
                            ->displayFormat('d.m.Y'),
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
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Додати підписку')),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('Переглянути')),
                EditAction::make()
                    ->label(__('Редагувати')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
