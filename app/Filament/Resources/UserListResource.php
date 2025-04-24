<?php

namespace App\Filament\Resources;

use App\Enums\UserListType;
use App\Filament\Resources\UserListResource\Pages;
use App\Filament\Resources\UserListResource\RelationManagers;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\UserList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Illuminate\Database\Eloquent\Model;

class UserListResource extends Resource
{
    protected static ?string $model = UserList::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Користувачі';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Списки користувачів');
    }

    public static function getModelLabel(): string
    {
        return __('Список користувача');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Списки користувачів');
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

                                Select::make('type')
                                    ->label(__('Тип списку'))
                                    ->options(UserListType::class)
                                    ->enum(UserListType::class)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
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
                            ]),
                    ]),
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
                    ->indicator(__('Користувач')),

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

                Filter::make('created_at')
                    ->label(__('Дата створення'))
                    ->form([
                        TextInput::make('created_from')
                            ->label(__('Від'))
                            ->type('date'),
                        TextInput::make('created_until')
                            ->label(__('До'))
                            ->type('date'),
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
                Action::make('view_content')
                    ->label(__('Переглянути контент'))
                    ->icon('heroicon-o-eye')
                    ->url(function (UserList $record) {
                        return match ($record->listable_type) {
                            Movie::class => route('filament.admin.resources.movies.edit', $record->listable_id),
                            Episode::class => route('filament.admin.resources.episodes.edit', $record->listable_id),
                            Person::class => route('filament.admin.resources.people.edit', $record->listable_id),
                            Tag::class => route('filament.admin.resources.tags.edit', $record->listable_id),
                            Selection::class => route('filament.admin.resources.selections.edit', $record->listable_id),
                            default => '#',
                        };
                    })
                    ->openUrlInNewTab(),

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
                'type',
                'listable_type',
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
            'index' => Pages\ListUserLists::route('/'),
            'create' => Pages\CreateUserList::route('/create'),
            'view' => Pages\ViewUserList::route('/{record}'),
            'edit' => Pages\EditUserList::route('/{record}/edit'),
        ];
    }
}