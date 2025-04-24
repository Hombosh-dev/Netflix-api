<?php

namespace App\Filament\Resources;

use App\Enums\CommentReportType;
use App\Filament\Resources\CommentReportResource\Pages;
use App\Models\CommentReport;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentReportResource extends Resource
{
    protected static ?string $model = CommentReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Модерація';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Скарги на коментарі');
    }

    public static function getModelLabel(): string
    {
        return __('Скарга на коментар');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Скарги на коментарі');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('Користувач'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('comment_id')
                    ->label(__('Коментар'))
                    ->relationship('comment', 'body')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->label(__('Тип скарги'))
                    ->options(CommentReportType::class)
                    ->enum(CommentReportType::class)
                    ->required(),

                Toggle::make('is_viewed')
                    ->label(__('Переглянуто'))
                    ->onIcon('heroicon-o-eye')
                    ->offIcon('heroicon-o-eye-slash')
                    ->onColor('success')
                    ->offColor('danger')
                    ->required(),

                Textarea::make('body')
                    ->label(__('Опис скарги'))
                    ->placeholder(__('Детальний опис проблеми'))
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

                TextColumn::make('user.name')
                    ->label(__('Скаржник'))
                    ->description(fn($record) => '@'.($record->user->username ?? 'unknown'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('comment.body')
                    ->label(__('Коментар'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->comment?->body ?? __('Коментар видалено'))
                    ->searchable(),

                TextColumn::make('comment.commentable_type')
                    ->label(__('Тип контенту'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'App\\Models\\Movie' => __('Фільм'),
                        'App\\Models\\Episode' => __('Епізод'),
                        'App\\Models\\Selection' => __('Підбірка'),
                        default => __('Невідомий контент'),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'App\\Models\\Movie' => 'success',
                        'App\\Models\\Episode' => 'info',
                        'App\\Models\\Selection' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('Тип скарги'))
                    ->badge()
                    ->formatStateUsing(fn(CommentReportType $state): string => $state->getLabel())
                    ->color(fn(CommentReportType $state): string => $state->getColor())
                    ->icon(fn(CommentReportType $state): string => $state->getIcon())
                    ->sortable(),

                TextColumn::make('is_viewed')
                    ->label(__('Статус'))
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state
                        ? __('Переглянуто')
                        : __('Непереглянуто')
                    )
                    ->icon(fn(bool $state): string => $state
                        ? 'heroicon-o-eye'
                        : 'heroicon-o-eye-slash'
                    )
                    ->color(fn(bool $state): string => $state
                        ? 'success'
                        : 'danger'
                    )
                    ->sortable(),

                ToggleColumn::make('is_viewed')
                    ->label(__('Переглянуто'))
                    ->onIcon('heroicon-s-eye')
                    ->offIcon('heroicon-s-eye-slash')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('Створено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label(__('Оновлено'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_viewed')
                    ->label(__('Статус перегляду'))
                    ->placeholder(__('Всі'))
                    ->trueLabel(__('Переглянуті'))
                    ->falseLabel(__('Непереглянуті'))
                    ->indicator(__('Статус перегляду'))
                    ->queries(
                        true: fn(Builder $query) => $query->where('is_viewed', true),
                        false: fn(Builder $query) => $query->where('is_viewed', false),
                        blank: fn(Builder $query) => $query,
                    ),

                SelectFilter::make('user_id')
                    ->label(__('Скаржник'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator(__('Скаржник')),

                Filter::make('created_at')
                    ->label(__('Дата створення'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('Від')),
                        DatePicker::make('created_until')
                            ->label(__('До')),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                SelectFilter::make('type')
                    ->label(__('Тип скарги'))
                    ->options(CommentReportType::class)
                    ->multiple()
                    ->indicator(__('Тип скарги')),

                SelectFilter::make('commentable_type')
                    ->label(__('Тип контенту'))
                    ->options([
                        'App\\Models\\Movie' => __('Фільм'),
                        'App\\Models\\Episode' => __('Епізод'),
                        'App\\Models\\Selection' => __('Підбірка'),
                    ])
                    ->multiple()
                    ->indicator(__('Тип контенту'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['values'],
                            fn(Builder $query, $types): Builder => $query->whereHas(
                                'comment',
                                fn(Builder $query) => $query->whereIn('commentable_type', $types)
                            )
                        );
                    }),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommentReports::route('/'),
            'create' => Pages\CreateCommentReport::route('/create'),
            'edit' => Pages\EditCommentReport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'comment']);
    }
}
