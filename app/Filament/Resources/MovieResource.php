<?php

namespace App\Filament\Resources;

use App\Enums\Kind;
use App\Filament\Resources\MovieResource\Pages;
use App\Filament\Resources\MovieResource\RelationManagers;
use App\Models\Movie;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovieResource extends Resource
{
    protected static ?string $model = Movie::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(128)
                            ->label('Посилання на фільм'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(248)
                            ->label('Назва фільму'),
                        TextArea::make('description')
                            ->rows(5)
                            ->required()
                            ->label('Опис фільму'),
                        FileUpload::make('image_name')
                            ->image()
                            ->label('Зображення'),
                        Repeater::make('aliases')
                            ->schema([
                                TextInput::make('alias')
                                    ->label('Інша назва')->required(),
                            ])
                            ->columns(1)
                            ->label('Інші назви'),
                        Repeater::make('Джерела (API)')
                            ->schema([
                                TextInput::make('source')
                                    ->label('Джерело')->required(),
                                TextInput::make('id')
                                    ->label('Ідентифікатор')->required(),
                            ])
                            ->columns(2)
                            ->label('Джерела фільму'),
                    ]),

                Section::make('Деталі')
                    ->schema([
                        Select::make('studio_id')
                            ->relationship('studio', 'name')
                            ->required()
                            ->label('Студія'),
                        Repeater::make('countries')
                            ->schema([
                                TextInput::make('country')
                                    ->label('Країна')->required(),
                            ])
                            ->columns(1)
                            ->label('Країни виробники'),
                        Select::make('kind')
                            ->options(Kind::class)
                            ->label('Вид'), # ASK OLEKSANDR
                        TextInput::make('poster')
                            ->maxLength(2048)
                            ->label('Посилання на постер'),
                        TextInput::make('duration')
                            ->numeric()
                            ->label('Тривалість'),
                        TextInput::make('episodes_count')
                            ->numeric()
                            ->label('Кількість епізодів'),
                        DatePicker::make('first_air_date')
                            ->label('Перша дата премʼєри'),
                        DatePicker::make('last_air_date')
                            ->label('Остання дата премʼєри'),
                        TextInput::make('imdb_score')
                            ->numeric()
                            ->label('Оцінка IMDb'),
                    ]),
                // Section for attachments and related movies
                Section::make('Вкладення та схожі фільми')
                    ->schema([
                        Repeater::make('attachments')
                            ->schema([
                                FileUpload::make('attachments')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->label('Вкладення')
                            ])
                            ->columns(1)
                            ->label('Вкладення'),
                        Repeater::make('related')
                            ->schema([
                                TextInput::make('related_movie')
                                    ->label('Схожі фільми')->required(),
                            ])
                            ->columns(1)
                            ->label('Схожі фільми'),
                    ]),

                Section::make('Мета-дані')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Опубліковано'),
                        TextInput::make('meta_title')
                            ->maxLength(128)
                            ->label('Мета-назва'),
                        TextInput::make('meta_description')
                            ->maxLength(376)
                            ->label('Мета-опис'),
                        FileUpload::make('meta_image')
                            ->image()
                            ->label('Мета-зображення'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('studio.name')
                    ->label('Studio')
                    ->sortable(),
                TextColumn::make('first_air_date')
                    ->date(),
                BooleanColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMovies::route('/'),
            'create' => Pages\CreateMovie::route('/create'),
            'edit'   => Pages\EditMovie::route('/{record}/edit'),
        ];
    }
}
