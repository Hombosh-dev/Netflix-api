<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Enums\Kind;
use App\Enums\Status;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MoviesRelationManager extends RelationManager
{
    protected static string $relationship = 'movies';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('character_name')
                    ->label(__('Ім\'я персонажа'))
                    ->required(),
                    
                Select::make('voice_person_id')
                    ->label(__('Актор озвучення'))
                    ->relationship('persons', 'name', fn (Builder $query) => $query->select(['id', 'name']))
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image_name')
                    ->label(__('Постер'))
                    ->circular(),
                    
                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('kind')
                    ->label(__('Тип'))
                    ->badge()
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label(__('Статус'))
                    ->badge()
                    ->sortable(),
                    
                TextColumn::make('character_name')
                    ->label(__('Персонаж'))
                    ->searchable(),
                    
                TextColumn::make('voicePerson.name')
                    ->label(__('Актор озвучення'))
                    ->searchable(),
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
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('character_name')
                            ->label(__('Ім\'я персонажа'))
                            ->required(),
                        Select::make('voice_person_id')
                            ->label(__('Актор озвучення'))
                            ->relationship('persons', 'name', fn (Builder $query) => $query->select(['id', 'name']))
                            ->searchable()
                            ->preload(),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ]);
    }
}
