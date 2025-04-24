<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserListsRelationManager extends RelationManager
{
    protected static string $relationship = 'userLists';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Списки');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label(__('Тип списку'))
                    ->options(UserListType::class)
                    ->enum(UserListType::class)
                    ->required(),

                Select::make('listable_type')
                    ->label(__('Тип контенту'))
                    ->options([
                        Movie::class => __('Фільм'),
                        Episode::class => __('Епізод'),
                        Person::class => __('Персона'),
                        Tag::class => __('Тег'),
                        Selection::class => __('Підбірка'),
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('listable_id', null)),

                Select::make('listable_id')
                    ->label(__('Контент'))
                    ->options(function (callable $get) {
                        $type = $get('listable_type');
                        if (!$type) return [];

                        return match ($type) {
                            Movie::class => Movie::all()->pluck('name', 'id'),
                            Episode::class => Episode::all()->pluck('name', 'id'),
                            Person::class => Person::all()->pluck('name', 'id'),
                            Tag::class => Tag::all()->pluck('name', 'id'),
                            Selection::class => Selection::all()->pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Тип списку'))
                    ->badge()
                    ->color(fn (UserListType $state): string => $state->getColor())
                    ->icon(fn (UserListType $state): string => $state->getIcon())
                    ->sortable(),

                TextColumn::make('listable_type')
                    ->label(__('Тип контенту'))
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            Movie::class => __('Фільм'),
                            Episode::class => __('Епізод'),
                            Person::class => __('Персона'),
                            Tag::class => __('Тег'),
                            Selection::class => __('Підбірка'),
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('listable.name')
                    ->label(__('Назва контенту'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Тип списку'))
                    ->options(UserListType::class)
                    ->multiple()
                    ->indicator(__('Тип списку')),

                SelectFilter::make('listable_type')
                    ->label(__('Тип контенту'))
                    ->options([
                        Movie::class => __('Фільм'),
                        Episode::class => __('Епізод'),
                        Person::class => __('Персона'),
                        Tag::class => __('Тег'),
                        Selection::class => __('Підбірка'),
                    ])
                    ->multiple()
                    ->indicator(__('Тип контенту')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Додати до списку')),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('Переглянути')),
                DeleteAction::make()
                    ->label(__('Видалити')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
