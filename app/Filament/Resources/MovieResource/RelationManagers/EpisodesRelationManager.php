<?php

namespace App\Filament\Resources\MovieResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')
                    ->label(__('Номер епізоду'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(fn () => 1),

                TextInput::make('name')
                    ->label(__('Назва епізоду'))
                    ->required()
                    ->maxLength(128)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (!$state) return;

                        // Завжди оновлюємо slug при редагуванні назви
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->maxLength(128),

                Textarea::make('description')
                    ->label(__('Опис'))
                    ->rows(5)
                    ->columnSpanFull(),

                TextInput::make('duration')
                    ->label(__('Тривалість (хв)'))
                    ->numeric()
                    ->minValue(1)
                    ->default(fn () => 45),

                DatePicker::make('air_date')
                    ->label(__('Дата виходу'))
                    ->default(fn () => Carbon::now()),

                Toggle::make('is_filler')
                    ->label(__('Філлер'))
                    ->helperText(__('Епізод, який не впливає на основний сюжет'))
                    ->default(false),

                FileUpload::make('pictures')
                    ->label(__('Зображення епізоду'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('episodes')
                    ->maxFiles(5)
                    ->columnSpanFull(),

                Repeater::make('video_players')
                    ->label(__('Відеоплеєри'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Назва'))
                            ->required(),

                        TextInput::make('url')
                            ->label(__('URL'))
                            ->url()
                            ->required(),

                        TextInput::make('file_url')
                            ->label(__('URL файлу'))
                            ->url(),

                        TextInput::make('dubbing')
                            ->label(__('Озвучення')),

                        Select::make('quality')
                            ->label(__('Якість'))
                            ->options([
                                'sd' => 'SD',
                                'hd' => 'HD',
                                'fhd' => 'Full HD',
                                '4k' => '4K',
                            ])
                            ->default('hd'),

                        TextInput::make('locale_code')
                            ->label(__('Код локалізації'))
                            ->default('uk'),
                    ])
                    ->defaultItems(1)
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('number')
                    ->label(__('№'))
                    ->sortable(),

                ImageColumn::make('pictureUrl')
                    ->label(__('Зображення'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->fullName),

                TextColumn::make('duration')
                    ->label(__('Тривалість'))
                    ->formatStateUsing(fn ($record) => $record->formattedDuration)
                    ->sortable(),

                TextColumn::make('air_date')
                    ->label(__('Дата виходу'))
                    ->date()
                    ->sortable(),

                IconColumn::make('is_filler')
                    ->label(__('Філлер'))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_filler')
                    ->label(__('Філлер'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Тільки філлери'))
                    ->falseLabel(__('Без філлерів'))
                    ->indicator(__('Філлер')),

                Filter::make('air_date')
                    ->label(__('Дата виходу'))
                    ->form([
                        DatePicker::make('aired_from')
                            ->label(__('Від')),
                        DatePicker::make('aired_until')
                            ->label(__('До')),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['aired_from'] ?? null) {
                            $indicators[] = __('Від') . ': ' . $data['aired_from'];
                        }

                        if ($data['aired_until'] ?? null) {
                            $indicators[] = __('До') . ': ' . $data['aired_until'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['aired_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('air_date', '>=', $date),
                            )
                            ->when(
                                $data['aired_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('air_date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('number');
    }
}
