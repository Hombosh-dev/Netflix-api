<?php

namespace App\Filament\Resources\SelectionResource\RelationManagers;

use App\Enums\Gender;
use App\Enums\PersonType;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Персони');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // No additional fields needed for the morph relationship
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Фото'))
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-avatar.png'))
                    ->size(60),

                TextColumn::make('name')
                    ->label(__('Ім\'я'))
                    ->description(fn($record) => $record->original_name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('Тип'))
                    ->badge()
                    ->color(fn(PersonType $state): string => match ($state) {
                        PersonType::ACTOR => 'success',
                        PersonType::DIRECTOR => 'warning',
                        PersonType::WRITER => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('gender')
                    ->label(__('Стать'))
                    ->badge()
                    ->color(fn(Gender $state): string => match ($state) {
                        Gender::MALE => 'info',
                        Gender::FEMALE => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('movies_count')
                    ->label(__('Фільми'))
                    ->counts('movies')
                    ->sortable(),
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
            ])
            ->headerActions([
                AttachAction::make()
                    ->label(__('Додати персони'))
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'original_name'])
            ])
            ->actions([
                DetachAction::make()
                    ->label(__('Видалити з підбірки')),
            ])
            ->bulkActions([
                DetachBulkAction::make()
                    ->label(__('Видалити вибрані')),
            ]);
    }
}
