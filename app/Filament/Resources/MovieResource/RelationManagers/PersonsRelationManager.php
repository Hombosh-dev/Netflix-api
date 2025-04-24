<?php

namespace App\Filament\Resources\MovieResource\RelationManagers;

use App\Models\Person;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

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
                    ->options(Person::all()->pluck('name', 'id'))
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
                    ->label(__('Фото'))
                    ->circular(),
                    
                TextColumn::make('name')
                    ->label(__('Ім\'я'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('character_name')
                    ->label(__('Персонаж'))
                    ->searchable(),
                    
                TextColumn::make('voicePerson.name')
                    ->label(__('Актор озвучення'))
                    ->searchable(),
            ])
            ->filters([
                //
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
                            ->options(Person::all()->pluck('name', 'id'))
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
