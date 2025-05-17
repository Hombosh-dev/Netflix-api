<?php

namespace App\Filament\Resources;

use App\Enums\VideoQuality;
use App\Filament\Resources\EpisodeResource\Pages;
use App\Filament\Resources\EpisodeResource\RelationManagers;
use App\Models\Episode;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Епізоди');
    }

    public static function getModelLabel(): string
    {
        return __('Епізод');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Епізоди');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Епізод')
                    ->tabs([
                        Tab::make(__('Основна інформація'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('movie_id')
                                            ->label(__('Фільм/Серіал'))
                                            ->relationship('movie', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        TextInput::make('number')
                                            ->label(__('Номер епізоду'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(fn() => 1),
                                    ]),

                                TextInput::make('name')
                                    ->label(__('Назва епізоду'))
                                    ->required()
                                    ->maxLength(128)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        if (!$state) {
                                            return;
                                        }
                                        $set('slug', Episode::generateSlug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label(__('Slug'))
                                    ->required()
                                    ->maxLength(128)
                                    ->unique(Episode::class, 'slug', ignoreRecord: true),

                                Textarea::make('description')
                                    ->label(__('Опис'))
                                    ->rows(5)
                                    ->columnSpanFull(),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('duration')
                                            ->label(__('Тривалість (хв)'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(fn() => 45),

                                        DatePicker::make('air_date')
                                            ->label(__('Дата виходу'))
                                            ->default(fn() => Carbon::now()),

                                        Toggle::make('is_filler')
                                            ->label(__('Філлер'))
                                            ->helperText(__('Епізод, який не впливає на основний сюжет'))
                                            ->default(false),
                                    ]),
                            ]),

                        Tab::make(__('Медіа'))
                            ->schema([
                                Section::make(__('Зображення'))
                                    ->schema([
                                        FileUpload::make('pictures')
                                            ->label(__('Зображення епізоду'))
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->directory('episodes')
                                            ->maxFiles(5)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('Відеоплеєри'))
                                    ->schema([
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
                                                    ->options(VideoQuality::class)
                                                    ->default('hd'),

                                                TextInput::make('locale_code')
                                                    ->label(__('Код локалізації'))
                                                    ->default('uk'),
                                            ])
                                            ->defaultItems(1)
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(__('SEO'))
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->placeholder(__('E{number}: {name} | {movie_name}'))
                                    ->maxLength(128),

                                Textarea::make('meta_description')
                                    ->label(__('Meta Description'))
                                    ->maxLength(376)
                                    ->rows(3),

                                FileUpload::make('meta_image')
                                    ->label(__('Meta Image'))
                                    ->image()
                                    ->directory('seo')
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
                    ->description(fn(Episode $record) => $record->fullName),

                TextColumn::make('movie.name')
                    ->label(__('Фільм/Серіал'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('duration')
                    ->label(__('Тривалість'))
                    ->formatStateUsing(fn(Episode $record) => $record->formattedDuration)
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
                SelectFilter::make('movie_id')
                    ->label(__('Фільм/Серіал'))
                    ->relationship('movie', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Фільм/Серіал')),

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
                            $indicators[] = __('Від').': '.$data['aired_from'];
                        }

                        if ($data['aired_until'] ?? null) {
                            $indicators[] = __('До').': '.$data['aired_until'];
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['aired_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('air_date', '>=', $date),
                            )
                            ->when(
                                $data['aired_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('air_date', '<=', $date),
                            );
                    }),

                Filter::make('recently_aired')
                    ->label(__('Нещодавно вийшли'))
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
                            fn(Builder $query, $days): Builder => $query->recentlyAired((int) $days),
                        );
                    }),
            ])
            ->actions([
                Action::make('view_movie')
                    ->label(__('Переглянути фільм'))
                    ->icon('heroicon-o-film')
                    ->url(fn(Episode $record) => route('filament.admin.resources.movies.edit', ['record' => $record->movie_id]))
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
            ->defaultSort('movie_id')
            ->groups([
                'movie.name',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'view' => Pages\ViewEpisode::route('/{record}'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['movie']);
    }
}
