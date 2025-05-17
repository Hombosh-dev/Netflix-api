<?php

namespace Database\Seeders\RealisticDataSeeder;

use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\PersonType;
use App\Enums\Status;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Studio;
use App\Models\Tag;
use App\ValueObjects\ApiSource;
use App\ValueObjects\Attachment;
use App\ValueObjects\RelatedMovie;
use App\ValueObjects\VideoPlayer;
use Illuminate\Support\Str;

class MovieSeeder
{
    // Реальні назви фільмів
    protected static $movieNames = [
        'Початок', 'Темний лицар', 'Матриця', 'Володар перснів', 'Зоряні війни',
        'Месники', 'Інтерстеллар', 'Гладіатор', 'Титанік', 'Аватар',
        'Форрест Гамп', 'Бійцівський клуб', 'Хрещений батько', 'Шоушенк', 'Леон',
        'Назад у майбутнє', 'Термінатор', 'Індіана Джонс', 'Джанго вільний', 'Престиж',
        'Втеча з Шоушенка', 'Зелена миля', 'Список Шиндлера', 'Кримінальне чтиво', 'Сам удома',
        'Гаррі Поттер', 'Пірати Карибського моря', 'Залізна людина', 'Джокер', 'Паразити',
    ];

    // Реальні назви серіалів
    protected static $tvSeriesNames = [
        'Гра престолів', 'Дивні дива', 'Чорнобиль', 'Пуститися берега', 'Шерлок',
        'Друзі', 'Офіс', 'Справжній детектив', 'Картковий будинок', 'Мандалорець',
        'Вікінги', 'Ходячі мерці', 'Чорне дзеркало', 'Світ Дикого Заходу', 'Корона',
        'Пуститися берега', 'Дуже дивні справи', 'Паперовий будинок', 'Відьмак', 'Хлопаки',
    ];

    // Реальні назви аніме
    protected static $animeNames = [
        'Наруто', 'Атака титанів', 'Ван Піс', 'Моя академія героїв', 'Клинок, що знищує демонів',
        'Токійський гуль', 'Смертельна нотатка', 'Ковбой Бібоп', 'Євангеліон', 'Привид у броні',
        'Стальний алхімік', 'Код Гіас', 'Ванпанчмен', 'Доктор Стоун', 'Чаклунка Мадока',
    ];

    // Реальні назви мультфільмів
    protected static $animatedMovieNames = [
        'Король Лев', 'Історія іграшок', 'Шрек', 'Крижане серце', 'Як приборкати дракона',
        'У пошуках Немо', 'ВАЛЛ-І', 'Суперсімейка', 'Зоотрополіс', 'Головоломка',
        'Аладдін', 'Красуня та чудовисько', 'Мулан', 'Рататуй', 'Душа',
    ];

    /**
     * Створення фільмів
     */
    public static function createMovies(int $count, callable $getRandomImagePath): array
    {
        $movies = [];
        $studios = Studio::all();
        $tags = Tag::all();
        $genres = Tag::where('is_genre', true)->get();

        // Створення фільмів
        for ($i = 0; $i < $count; $i++) {
            // Визначення типу контенту
            $kind = fake()->randomElement(Kind::cases());

            // Вибір назви залежно від типу
            switch ($kind) {
                case Kind::MOVIE:
                    $name = self::$movieNames[array_rand(self::$movieNames)];
                    break;
                case Kind::TV_SERIES:
                    $name = self::$tvSeriesNames[array_rand(self::$tvSeriesNames)];
                    break;
                case Kind::ANIMATED_MOVIE:
                    $name = self::$animatedMovieNames[array_rand(self::$animatedMovieNames)];
                    break;
                case Kind::ANIMATED_SERIES:
                    $name = self::$animeNames[array_rand(self::$animeNames)];
                    break;
                default:
                    $name = fake()->sentence(3);
            }

            // Додаємо унікальний ідентифікатор до назви, щоб уникнути дублікатів
            $name = $name . ' ' . ($i + 1);

            // Створення фільму
            $movie = Movie::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => self::generateMovieDescription($name, $kind),
                'kind' => $kind,
                'status' => fake()->randomElement(Status::cases()),
                'studio_id' => $studios->random()->id,
                'image_name' => $getRandomImagePath('movies'),
                'poster' => $getRandomImagePath('posters'),
                'countries' => self::generateCountries(),
                'aliases' => self::generateMovieAliases($name),
                'first_air_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'last_air_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-5 years', '+1 year')->format('Y-m-d') : null,
                'duration' => in_array($kind, [Kind::MOVIE, Kind::ANIMATED_MOVIE]) ? fake()->numberBetween(90, 180) : null,
                'episodes_count' => in_array($kind, [Kind::TV_SERIES, Kind::ANIMATED_SERIES]) ? fake()->numberBetween(10, 100) : null,
                'imdb_score' => fake()->randomFloat(1, 5.0, 9.9),
                'api_sources' => self::generateApiSources(),
                'attachments' => self::generateAttachments($getRandomImagePath),
                'related' => [],  // Буде заповнено пізніше
                'similars' => [], // Буде заповнено пізніше
                'is_published' => fake()->boolean(80),
                'meta_title' => $name . ' дивитися онлайн | Netflix',
                'meta_description' => "Дивіться {$name} онлайн на Netflix. Висока якість, без реклами.",
                'meta_image' => $getRandomImagePath('movies_meta'),
            ]);

            // Додавання тегів
            $genreCount = fake()->numberBetween(1, 3);
            $tagCount = fake()->numberBetween(2, 5);

            // Вибираємо випадкові жанри та теги
            $selectedGenres = $genres->random($genreCount)->pluck('id')->toArray();
            $selectedTags = $tags->random($tagCount)->pluck('id')->toArray();

            // Об'єднуємо та видаляємо дублікати
            $allTags = array_unique(array_merge($selectedGenres, $selectedTags));

            // Прикріплюємо всі теги одним запитом
            $movie->tags()->attach($allTags);

            $movies[] = $movie;
        }

        return $movies;
    }

    /**
     * Додавання зв'язків між фільмами
     */
    public static function createMovieRelations(array $movies): void
    {
        foreach ($movies as $movie) {
            // Додавання пов'язаних фільмів
            $relatedCount = fake()->numberBetween(0, 3);
            $relatedMovies = [];

            if ($relatedCount > 0) {
                $otherMovies = collect($movies)->where('id', '!=', $movie->id)->random($relatedCount);

                foreach ($otherMovies as $otherMovie) {
                    $relatedMovies[] = new RelatedMovie(
                        $otherMovie->id,
                        fake()->randomElement(MovieRelateType::cases())
                    );
                }

                $movie->related = $relatedMovies;
                $movie->save();
            }

            // Додавання схожих фільмів
            $similarCount = fake()->numberBetween(0, 5);
            $similarMovies = [];

            if ($similarCount > 0) {
                $otherMovies = collect($movies)->where('id', '!=', $movie->id)->random($similarCount);

                foreach ($otherMovies as $otherMovie) {
                    $similarMovies[] = $otherMovie->id;
                }

                $movie->similars = $similarMovies;
                $movie->save();
            }
        }
    }

    /**
     * Додавання персон до фільмів
     */
    public static function addPeopleToMovies(array $movies): void
    {
        $actors = Person::where('type', PersonType::ACTOR)->get();
        $directors = Person::where('type', PersonType::DIRECTOR)->get();
        $characters = Person::where('type', PersonType::CHARACTER)->get();
        $voiceActors = Person::where('type', PersonType::VOICE_ACTOR)->get();
        $otherPersons = Person::whereNotIn('type', [PersonType::ACTOR, PersonType::DIRECTOR, PersonType::CHARACTER])->get();

        foreach ($movies as $movie) {
            // Додавання режисерів
            $directorCount = fake()->numberBetween(1, 2);
            $movieDirectors = $directors->random($directorCount);

            foreach ($movieDirectors as $director) {
                $movie->persons()->attach($director->id, [
                    'character_name' => 'Режисер',
                ]);
            }

            // Додавання акторів або персонажів залежно від типу фільму
            if (in_array($movie->kind, [Kind::ANIMATED_MOVIE, Kind::ANIMATED_SERIES])) {
                // Для анімації додаємо персонажів та озвучку
                $characterCount = fake()->numberBetween(3, 8);
                $movieCharacters = $characters->random(min($characterCount, $characters->count()));

                foreach ($movieCharacters as $character) {
                    // Додаємо озвучку для деяких персонажів
                    $voicePersonId = fake()->boolean(70) ? $voiceActors->random()->id : null;

                    $movie->persons()->attach($character->id, [
                        'character_name' => $character->name,
                        'voice_person_id' => $voicePersonId,
                    ]);
                }
            } else {
                // Для звичайних фільмів додаємо акторів
                $actorCount = fake()->numberBetween(5, 15);
                $movieActors = $actors->random(min($actorCount, $actors->count()));

                foreach ($movieActors as $actor) {
                    $movie->persons()->attach($actor->id, [
                        'character_name' => fake()->name(),
                    ]);
                }
            }

            // Додавання інших персон (продюсери, сценаристи, тощо)
            $otherCount = fake()->numberBetween(2, 5);
            $movieOthers = $otherPersons->random(min($otherCount, $otherPersons->count()));

            foreach ($movieOthers as $other) {
                $movie->persons()->attach($other->id, [
                    'character_name' => $other->type->getLabel() ?? $other->type->value,
                ]);
            }
        }
    }

    /**
     * Створення епізодів для серіалів
     */
    public static function createEpisodes(array $movies, callable $getRandomImagePath, int $minEpisodes, int $maxEpisodes): void
    {
        foreach ($movies as $movie) {
            // Створюємо епізоди тільки для серіалів
            if (!in_array($movie->kind, [Kind::TV_SERIES, Kind::ANIMATED_SERIES])) {
                continue;
            }

            // Визначаємо кількість епізодів
            $episodesCount = fake()->numberBetween($minEpisodes, $maxEpisodes);

            for ($i = 1; $i <= $episodesCount; $i++) {
                $episodeName = "Епізод {$i}";

                if (fake()->boolean(70)) {
                    // Додаємо назву епізоду
                    $episodeName .= ": " . fake()->sentence(3);
                }

                Episode::create([
                    'movie_id' => $movie->id,
                    'number' => $i,
                    'name' => $episodeName,
                    'slug' => Str::slug($movie->name . '-episode-' . $i),
                    'description' => self::generateEpisodeDescription($movie->name, $i),
                    'duration' => fake()->numberBetween(20, 60),
                    'air_date' => fake()->dateTimeBetween($movie->first_air_date, max($movie->first_air_date, $movie->last_air_date ?? 'now'))->format('Y-m-d'),
                    'is_filler' => fake()->boolean(10), // 10% шанс бути філлером
                    'pictures' => self::generatePictures($getRandomImagePath, 'episodes', fake()->numberBetween(1, 5)),
                    'video_players' => self::generateVideoPlayers(),
                    'meta_title' => "{$episodeName} | {$movie->name} | Netflix",
                    'meta_description' => "Дивіться {$episodeName} серіалу {$movie->name} онлайн на Netflix.",
                    'meta_image' => $getRandomImagePath('episodes_meta'),
                ]);
            }

            // Оновлюємо кількість епізодів у фільмі
            $movie->episodes_count = $episodesCount;
            $movie->save();
        }
    }

    /**
     * Генерація опису для фільму
     */
    protected static function generateMovieDescription(string $name, Kind $kind): string
    {
        $descriptions = [];

        switch ($kind) {
            case Kind::MOVIE:
                $descriptions = [
                    "Фільм {$name} розповідає захоплюючу історію, яка не залишить байдужим жодного глядача.",
                    "{$name} - це драматична історія про людські стосунки, кохання та зраду.",
                    "У фільмі {$name} глядачі побачать неймовірні пригоди головних героїв у боротьбі за справедливість.",
                    "{$name} - це історія про звичайну людину, яка опиняється в надзвичайних обставинах.",
                    "Фільм {$name} розкриває глибокі філософські питання через захоплюючий сюжет та яскравих персонажів.",
                ];
                break;

            case Kind::TV_SERIES:
                $descriptions = [
                    "Серіал {$name} розповідає захоплюючу історію, яка розгортається протягом кількох сезонів.",
                    "{$name} - це драматичний серіал про складні людські стосунки та життєві випробування.",
                    "У серіалі {$name} глядачі побачать неймовірні пригоди головних героїв у різних ситуаціях.",
                    "{$name} - це історія про звичайних людей, які опиняються в надзвичайних обставинах.",
                    "Серіал {$name} розкриває глибокі філософські питання через захоплюючий сюжет та яскравих персонажів.",
                ];
                break;

            case Kind::ANIMATED_MOVIE:
                $descriptions = [
                    "Мультфільм {$name} розповідає захоплюючу історію, яка сподобається глядачам будь-якого віку.",
                    "{$name} - це анімаційна пригода з яскравими персонажами та важливими життєвими уроками.",
                    "У мультфільмі {$name} глядачі побачать неймовірні пригоди головних героїв у фантастичному світі.",
                    "{$name} - це історія про дружбу, відвагу та самопізнання, розказана через анімацію.",
                    "Мультфільм {$name} поєднує в собі захоплюючий сюжет, яскраву анімацію та глибокий сенс.",
                ];
                break;

            case Kind::ANIMATED_SERIES:
                $descriptions = [
                    "Аніме {$name} розповідає захоплюючу історію, яка розгортається протягом кількох сезонів.",
                    "{$name} - це анімаційний серіал з яскравими персонажами та глибоким сюжетом.",
                    "У аніме {$name} глядачі побачать неймовірні пригоди головних героїв у фантастичному світі.",
                    "{$name} - це історія про дружбу, відвагу та самопізнання, розказана через японську анімацію.",
                    "Аніме {$name} поєднує в собі захоплюючий сюжет, яскраву анімацію та глибокі філософські питання.",
                ];
                break;

            default:
                $descriptions = [
                    "{$name} - це захоплююча історія, яка не залишить байдужим жодного глядача.",
                    "У {$name} глядачі побачать неймовірні пригоди головних героїв у різних ситуаціях.",
                    "{$name} розкриває глибокі філософські питання через захоплюючий сюжет та яскравих персонажів.",
                    "Історія {$name} розгортається в унікальному світі з власними правилами та законами.",
                    "{$name} - це суміш різних жанрів, яка створює унікальний глядацький досвід.",
                ];
                break;
        }

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraphs(3, true);
    }

    /**
     * Генерація опису для епізоду
     */
    protected static function generateEpisodeDescription(string $seriesName, int $episodeNumber): string
    {
        $descriptions = [
            "У {$episodeNumber}-му епізоді серіалу {$seriesName} головні герої стикаються з новими викликами.",
            "Епізод {$episodeNumber} серіалу {$seriesName} розкриває нові таємниці та повороти сюжету.",
            "У цьому епізоді серіалу {$seriesName} глядачі дізнаються більше про минуле головних героїв.",
            "Епізод {$episodeNumber} продовжує захоплюючу історію серіалу {$seriesName} новими подіями.",
            "У цій серії {$seriesName} відбуваються ключові події, які змінюють хід всього сюжету.",
        ];

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraph(3);
    }

    /**
     * Генерація альтернативних назв для фільму
     */
    protected static function generateMovieAliases(string $name): array
    {
        $aliases = [];

        // Додаємо англійську назву
        if (fake()->boolean(80)) {
            $aliases[] = fake()->sentence(3);
        }

        // Додаємо скорочену назву
        if (str_word_count($name) > 1 && fake()->boolean(60)) {
            $words = explode(' ', $name);
            $acronym = '';
            foreach ($words as $word) {
                $acronym .= strtoupper(substr($word, 0, 1));
            }
            $aliases[] = $acronym;
        }

        // Додаємо альтернативну назву
        if (fake()->boolean(40)) {
            $aliases[] = $name . ': ' . fake()->words(3, true);
        }

        return $aliases;
    }

    /**
     * Генерація країн
     */
    protected static function generateCountries(): array
    {
        $countries = [
            'Україна', 'США', 'Велика Британія', 'Франція', 'Німеччина',
            'Італія', 'Іспанія', 'Канада', 'Австралія', 'Японія',
            'Південна Корея', 'Китай', 'Індія', 'Бразилія', 'Мексика',
        ];

        $count = fake()->numberBetween(1, 3);
        $selectedCountries = [];

        for ($i = 0; $i < $count; $i++) {
            $selectedCountries[] = $countries[array_rand($countries)];
        }

        return array_unique($selectedCountries);
    }

    /**
     * Генерація API джерел
     */
    protected static function generateApiSources(): array
    {
        $sources = [];

        // Додаємо TMDB джерело
        if (fake()->boolean(90)) {
            $sources[] = new ApiSource(
                ApiSourceName::TMDB,
                (string) fake()->numberBetween(1000, 9999)
            );
        }

        // Додаємо IMDB джерело
        if (fake()->boolean(80)) {
            $sources[] = new ApiSource(
                ApiSourceName::IMDB,
                'tt' . fake()->numberBetween(1000000, 9999999)
            );
        }

        // Додаємо інші джерела
        foreach ([ApiSourceName::SHIKI, ApiSourceName::ANILIST] as $source) {
            if (fake()->boolean(30)) {
                $sources[] = new ApiSource(
                    $source,
                    (string) fake()->numberBetween(10000, 99999)
                );
            }
        }

        return $sources;
    }

    /**
     * Генерація вкладень (трейлери, тизери, тощо)
     */
    protected static function generateAttachments(callable $getRandomImagePath): array
    {
        $attachments = [];

        // Додаємо трейлер
        if (fake()->boolean(90)) {
            $attachments[] = new Attachment(
                AttachmentType::TRAILER,
                'https://www.youtube.com/watch?v=' . Str::random(11),
                'Офіційний трейлер',
                fake()->numberBetween(90, 180)
            );
        }

        // Додаємо тизер
        if (fake()->boolean(70)) {
            $attachments[] = new Attachment(
                AttachmentType::TEASER,
                'https://www.youtube.com/watch?v=' . Str::random(11),
                'Тизер',
                fake()->numberBetween(30, 60)
            );
        }

        // Додаємо інші типи вкладень
        $otherTypes = [
            AttachmentType::BEHIND_THE_SCENES,
            AttachmentType::INTERVIEW,
            AttachmentType::CLIP,
            AttachmentType::DELETED_SCENE,
            AttachmentType::BLOOPER,
            AttachmentType::FEATURETTE,
        ];

        foreach ($otherTypes as $type) {
            if (fake()->boolean(30)) {
                $attachments[] = new Attachment(
                    $type,
                    'https://www.youtube.com/watch?v=' . Str::random(11),
                    $type->getLabel() ?? $type->value,
                    fake()->numberBetween(60, 300)
                );
            }
        }

        // Додаємо зображення
        $pictureCount = fake()->numberBetween(1, 5);
        for ($i = 0; $i < $pictureCount; $i++) {
            $attachments[] = new Attachment(
                AttachmentType::PICTURE,
                $getRandomImagePath('movie_pictures'),
                'Зображення ' . ($i + 1),
                0
            );
        }

        return $attachments;
    }

    /**
     * Генерація зображень
     */
    protected static function generatePictures(callable $getRandomImagePath, string $category, int $count): array
    {
        $pictures = [];

        for ($i = 0; $i < $count; $i++) {
            $pictures[] = $getRandomImagePath($category . '_pictures');
        }

        return $pictures;
    }

    /**
     * Генерація відеоплеєрів
     */
    protected static function generateVideoPlayers(): array
    {
        $players = [];

        // Додаємо Kodik плеєр
        if (fake()->boolean(90)) {
            $players[] = new VideoPlayer(
                VideoPlayerName::KODIK,
                'https://kodik.info/video/' . Str::random(8),
                'https://kodik.info/video/' . Str::random(8) . '/720p.mp4',
                fake()->randomElement(['Українська', 'Оригінал', 'Багатоголосий', 'Дубляж']),
                fake()->randomElement([VideoQuality::SD, VideoQuality::HD, VideoQuality::FULL_HD]),
                'uk'
            );
        }

        // Додаємо Aloha плеєр
        if (fake()->boolean(70)) {
            $players[] = new VideoPlayer(
                VideoPlayerName::ALOHA,
                'https://aloha.com/video/' . Str::random(8),
                'https://aloha.com/video/' . Str::random(8) . '/720p.mp4',
                fake()->randomElement(['Українська', 'Оригінал', 'Багатоголосий', 'Дубляж']),
                fake()->randomElement([VideoQuality::SD, VideoQuality::HD, VideoQuality::FULL_HD, VideoQuality::UHD]),
                'uk'
            );
        }

        return $players;
    }
}
