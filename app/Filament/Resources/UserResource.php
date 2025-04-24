<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Користувачі';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Користувачі');
    }

    public static function getModelLabel(): string
    {
        return __('Користувач');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Користувачі');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('Користувач'))
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Ім\'я'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('email')
                                            ->label(__('Email'))
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('role')
                                            ->label(__('Роль'))
                                            ->options(Role::class)
                                            ->required(),

                                        Select::make('gender')
                                            ->label(__('Стать'))
                                            ->options(Gender::class)
                                            ->nullable(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('birthday')
                                            ->label(__('Дата народження'))
                                            ->nullable(),

                                        DateTimePicker::make('email_verified_at')
                                            ->label(__('Дата верифікації email'))
                                            ->nullable(),
                                    ]),

                                TextInput::make('password')
                                    ->label(__('Пароль'))
                                    ->password()
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->maxLength(248)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Медіа'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('avatar')
                                            ->label(__('Аватар'))
                                            ->maxLength(2048),

                                        TextInput::make('backdrop')
                                            ->label(__('Фон'))
                                            ->maxLength(2048),
                                    ]),
                            ]),

                        Tab::make(__('Налаштування'))
                            ->schema([
                                Section::make(__('Контент'))
                                    ->schema([
                                        Toggle::make('allow_adult')
                                            ->label(__('Дозволити контент для дорослих'))
                                            ->default(false),
                                    ]),

                                Section::make(__('Відтворення'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_auto_next')
                                                    ->label(__('Автоматичний перехід до наступного'))
                                                    ->default(false),

                                                Toggle::make('is_auto_play')
                                                    ->label(__('Автоматичне відтворення'))
                                                    ->default(false),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_auto_skip_intro')
                                                    ->label(__('Автоматично пропускати інтро'))
                                                    ->default(false),

                                                Toggle::make('is_private_favorites')
                                                    ->label(__('Приватні вподобання'))
                                                    ->default(false),
                                            ]),
                                    ]),

                                Section::make(__('Модерація'))
                                    ->schema([
                                        Toggle::make('is_banned')
                                            ->label(__('Заблокований'))
                                            ->default(false),
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

                ImageColumn::make('avatar')
                    ->label(__('Аватар'))
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('Ім\'я'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label(__('Роль'))
                    ->badge()
                    ->color(fn (Role $state): string => $state->getColor())
                    ->icon(fn (Role $state): string => $state->getIcon())
                    ->sortable(),

                TextColumn::make('gender')
                    ->label(__('Стать'))
                    ->badge()
                    ->color(fn (?Gender $state): ?string => $state?->getColor())
                    ->formatStateUsing(fn (?Gender $state): ?string => $state?->getLabel())
                    ->sortable(),

                TextColumn::make('birthday')
                    ->label(__('Дата народження'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('last_seen_at')
                    ->label(__('Останній візит'))
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn (?Carbon $state, User $record): ?string => $record->formattedLastSeen)
                    ->toggleable(),

                IconColumn::make('is_banned')
                    ->label(__('Заблокований'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('allow_adult')
                    ->label(__('18+'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('role')
                    ->label(__('Роль'))
                    ->options(Role::class)
                    ->multiple()
                    ->indicator(__('Роль')),

                SelectFilter::make('gender')
                    ->label(__('Стать'))
                    ->options(Gender::class)
                    ->multiple()
                    ->indicator(__('Стать')),

                TernaryFilter::make('is_banned')
                    ->label(__('Статус блокування'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Заблоковані'))
                    ->falseLabel(__('Активні'))
                    ->indicator(__('Статус')),

                TernaryFilter::make('allow_adult')
                    ->label(__('Доступ до 18+'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Дозволено'))
                    ->falseLabel(__('Заборонено'))
                    ->indicator(__('18+')),

                Filter::make('created_at')
                    ->label(__('Дата реєстрації'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('Від')),
                        DatePicker::make('created_until')
                            ->label(__('До')),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('Створено від') . ' ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('Створено до') . ' ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),

                Filter::make('last_seen_at')
                    ->label(__('Останній візит'))
                    ->form([
                        DatePicker::make('last_seen_from')
                            ->label(__('Від')),
                        DatePicker::make('last_seen_until')
                            ->label(__('До')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['last_seen_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_seen_at', '>=', $date),
                            )
                            ->when(
                                $data['last_seen_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_seen_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['last_seen_from'] ?? null) {
                            $indicators[] = __('Останній візит від') . ' ' . Carbon::parse($data['last_seen_from'])->toFormattedDateString();
                        }

                        if ($data['last_seen_until'] ?? null) {
                            $indicators[] = __('Останній візит до') . ' ' . Carbon::parse($data['last_seen_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
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
                'role',
                'gender',
                'is_banned',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserListsRelationManager::class,
            RelationManagers\RatingsRelationManager::class,
            RelationManagers\UserSubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
