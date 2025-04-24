<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudioResource\Pages;
use App\Filament\Resources\StudioResource\RelationManagers;
use App\Models\Studio;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('Студії');
    }

    public static function getModelLabel(): string
    {
        return __('Студія');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Студії');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Студія')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Назва'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set) {
                                                if (!$state) {
                                                    return;
                                                }

                                                // Завжди оновлюємо slug при редагуванні назви
                                                $set('slug', Studio::generateSlug($state));
                                                $set('meta_title', Studio::makeMetaTitle($state));
                                            }),

                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->maxLength(128)
                                            ->unique(Studio::class, 'slug', ignoreRecord: true)
                                            ->helperText(__('Унікальний ідентифікатор для URL')),
                                    ]),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->required()
                                    ->maxLength(512)
                                    ->rows(5)
                                    ->placeholder(__('Детальний опис студії'))
                                    ->columnSpanFull(),

                                TagsInput::make('aliases')
                                    ->label(__('Альтернативні назви'))
                                    ->placeholder(__('Додайте альтернативні назви'))
                                    ->helperText(__('Альтернативні назви або абревіатури студії'))
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Медіа'))
                            ->schema([
                                Section::make(__('Зображення'))
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label(__('Логотип'))
                                            ->image()
                                            ->directory('studios')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('300')
                                            ->imageResizeTargetHeight('300')
                                            ->helperText(__('Рекомендований розмір: 300x300 пікселів'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('SEO'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->placeholder('{name} | Netflix')
                                    ->maxLength(128)
                                    ->helperText(__('Заголовок для пошукових систем')),

                                Textarea::make('meta_description')
                                    ->label(__('Meta Description'))
                                    ->maxLength(192)
                                    ->rows(3)
                                    ->placeholder(__('Опис для пошукових систем'))
                                    ->helperText(__('Оптимальна довжина: 150-160 символів')),

                                FileUpload::make('meta_image')
                                    ->label(__('Meta Image'))
                                    ->image()
                                    ->directory('seo')
                                    ->helperText(__('Зображення для соціальних мереж. Рекомендований розмір: 1200x630 пікселів'))
                                    ->columnSpanFull(),
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

                ImageColumn::make('image')
                    ->label(__('Логотип'))
                    ->defaultImageUrl(fn () => asset('images/default-studio.png'))
                    ->circular()
                    ->size(60),

                TextColumn::make('name')
                    ->label(__('Назва'))
                    ->description(fn ($record) => $record->aliases ? $record->aliases->join(', ') : null)
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('movies_count')
                    ->label(__('Фільми'))
                    ->counts('movies')
                    ->sortable()
                    ->color(fn (int $state): string => match(true) {
                        $state > 10 => 'success',
                        $state > 5 => 'warning',
                        $state > 0 => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable()
                    ->toggleable(),

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
                Filter::make('has_movies')
                    ->label(__('З фільмами'))
                    ->query(fn (Builder $query): Builder => $query->withMovies())
                    ->toggle()
                    ->indicator(__('Фільми')),

                Filter::make('created_at')
                    ->label(__('Дата створення'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('Від'))
                            ->displayFormat('d.m.Y'),
                        DatePicker::make('created_until')
                            ->label(__('До'))
                            ->displayFormat('d.m.Y'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('Від').': '.$data['created_from'];
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('До').': '.$data['created_until'];
                        }

                        return $indicators;
                    })
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

                Filter::make('recently_added')
                    ->label(__('Нещодавно додані'))
                    ->form([
                        TextInput::make('days')
                            ->label(__('Кількість днів'))
                            ->numeric()
                            ->default(7),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['days'] ?? null) {
                            return __('За останні').' '.$data['days'].' '.__('днів');
                        }

                        return null;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['days'],
                            fn (Builder $query, $days): Builder => $query->where('created_at', '>=', Carbon::now()->subDays((int) $days)),
                        );
                    }),
            ])
            ->actions([
                Action::make('view_movies')
                    ->label(__('Переглянути фільми'))
                    ->icon('heroicon-o-film')
                    ->url(fn (Studio $record) => route('filament.admin.resources.movies.index', ['tableFilters[studio][value]' => $record->id]))
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
            ->defaultSort('created_at', 'desc');
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\MoviesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'view' => Pages\ViewStudio::route('/{record}'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['movies']);
    }
}