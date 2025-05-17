<?php

namespace Database\Seeders;

use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\Gender;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\PersonType;
use App\Enums\Role;
use App\Enums\Status;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;
use App\ValueObjects\ApiSource;
use App\ValueObjects\Attachment;
use App\ValueObjects\RelatedMovie;
use App\ValueObjects\VideoPlayer;
use Database\Seeders\RealisticDataSeeder\MovieSeeder;
use Database\Seeders\RealisticDataSeeder\SelectionSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RealisticDataSeeder extends Seeder
{
    // Шляхи до тестових файлів
    protected $testImagesPath = 'test_files/images';
    protected $testVideosPath = 'test_files/videos';

    // Кількість записів для створення
    protected $studiosCount = 15;
    protected $tagsCount = 30;
    protected $genresCount = 20;
    protected $peopleCount = 100;
    protected $moviesCount = 50;
    protected $episodesPerSeriesMin = 5;
    protected $episodesPerSeriesMax = 15;
    protected $selectionsCount = 10;

    /**
     * Запуск сідера
     */
    public function run(): void
    {
        // Створення директорій для тестових файлів
        $this->createTestDirectories();

        // Завантаження тестових файлів
        $this->downloadTestFiles();

        // Створення адміністратора для Filament
        $this->createFilamentAdmin();

        // Створення даних
        $this->createStudios();
        $this->createTags();
        $this->createPeople();
        $this->createMovies();
        $this->createSelections();

        $this->command->info('Реалістичні дані успішно створені!');
    }

    /**
     * Створення адміністратора для Filament
     */
    protected function createFilamentAdmin(): void
    {
        $this->command->info('Створення адміністратора для Filament...');

        // Перевіряємо, чи існує користувач з email admin@example.com
        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command->info('Адміністратор вже існує.');
            return;
        }

        // Створюємо адміністратора
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => Role::ADMIN,
            'allow_adult' => true,
            'is_banned' => false,
        ]);

        $this->command->info('Адміністратор успішно створений!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Пароль: password');
    }

    /**
     * Створення директорій для тестових файлів
     */
    protected function createTestDirectories(): void
    {
        $this->command->info('Створення директорій для тестових файлів...');

        // Створення директорій для зображень
        if (!Storage::exists($this->testImagesPath)) {
            Storage::makeDirectory($this->testImagesPath);
        }

        // Створення директорій для відео
        if (!Storage::exists($this->testVideosPath)) {
            Storage::makeDirectory($this->testVideosPath);
        }
    }

    /**
     * Завантаження тестових файлів
     */
    protected function downloadTestFiles(): void
    {
        $this->command->info('Завантаження тестових файлів...');

        // Завантаження зображень з Picsum Photos
        $this->downloadImages();

        // Завантаження відео
        $this->downloadVideos();
    }

    /**
     * Завантаження зображень
     */
    protected function downloadImages(): void
    {
        $categories = [
            'studios', 'studios_meta',
            'genres', 'genres_meta',
            'tags', 'tags_meta',
            'actors', 'actors_meta',
            'directors', 'directors_meta',
            'characters', 'characters_meta',
            'people', 'people_meta',
            'movies', 'movies_meta',
            'posters',
            'movie_pictures',
            'episodes', 'episodes_meta',
            'episodes_pictures',
            'selections_meta',
        ];

        foreach ($categories as $category) {
            $this->command->info("Завантаження зображень для категорії: {$category}");

            $path = $this->testImagesPath . '/' . $category;

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            // Завантаження 5 зображень для кожної категорії
            for ($i = 1; $i <= 5; $i++) {
                $url = "https://picsum.photos/seed/{$category}-{$i}/800/600";
                $filename = "{$i}.jpg";

                try {
                    $contents = file_get_contents($url);
                    Storage::put($path . '/' . $filename, $contents);
                } catch (\Exception $e) {
                    $this->command->error("Помилка завантаження зображення: {$url}");
                }
            }
        }
    }

    /**
     * Завантаження відео
     */
    protected function downloadVideos(): void
    {
        $this->command->info('Завантаження тестових відео...');

        // Створення директорії для відео
        $path = $this->testVideosPath . '/samples';

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        // Завантаження тестового відео з публічного джерела
        $videoUrls = [
            'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
            'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_2mb.mp4',
        ];

        foreach ($videoUrls as $index => $url) {
            $filename = "sample_" . ($index + 1) . ".mp4";

            try {
                $contents = file_get_contents($url);
                Storage::put($path . '/' . $filename, $contents);
                $this->command->info("Відео завантажено: {$filename}");
            } catch (\Exception $e) {
                $this->command->error("Помилка завантаження відео: {$url}");
            }
        }
    }

    /**
     * Створення студій
     */
    protected function createStudios(): void
    {
        $this->command->info('Створення студій...');

        // Список реальних кіностудій
        $studioNames = [
            'Warner Bros. Pictures',
            'Universal Pictures',
            'Paramount Pictures',
            'Walt Disney Pictures',
            '20th Century Studios',
            'Sony Pictures',
            'Columbia Pictures',
            'Metro-Goldwyn-Mayer',
            'Lionsgate Films',
            'New Line Cinema',
            'DreamWorks Pictures',
            'Pixar Animation Studios',
            'Marvel Studios',
            'Lucasfilm',
            'A24',
            'Blumhouse Productions',
            'Focus Features',
            'Studio Ghibli',
            'Toho Company',
            'BBC Films',
        ];

        // Перемішуємо та обмежуємо кількість
        shuffle($studioNames);
        $studioNames = array_slice($studioNames, 0, $this->studiosCount);

        // Підготовка для унікальних слагів
        $existingSlugs = [];

        foreach ($studioNames as $name) {
            $slug = Str::slug($name);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $slug;
            while (in_array($slug, $existingSlugs)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Add to existing slugs
            $existingSlugs[] = $slug;

            // Створення студії
            Studio::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $this->generateStudioDescription($name),
                'image' => $this->getRandomImagePath('studios'),
                'aliases' => $this->generateAliases($name),
                'api_sources' => $this->generateApiSources(),
                'meta_title' => $name . ' | Netflix',
                'meta_description' => "Фільми та серіали від студії {$name}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('studios_meta'),
            ]);
        }
    }

    /**
     * Генерація опису для студії
     */
    protected function generateStudioDescription(string $name): string
    {
        $descriptions = [
            "{$name} - одна з найвідоміших кіностудій у світі, яка створила безліч культових фільмів та серіалів.",
            "Студія {$name} відома своїми інноваційними підходами до кіновиробництва та високоякісними проектами.",
            "{$name} спеціалізується на створенні блокбастерів та фільмів, які отримують визнання критиків.",
            "Заснована у XX столітті, студія {$name} стала однією з найвпливовіших у кіноіндустрії.",
            "Кіностудія {$name} відома своїми амбітними проектами та співпрацею з найкращими режисерами та акторами.",
        ];

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraph(3);
    }

    /**
     * Генерація альтернативних назв
     */
    protected function generateAliases(string $name): array
    {
        $aliases = [];

        // Додаємо скорочену назву
        if (str_word_count($name) > 1) {
            $words = explode(' ', $name);
            $acronym = '';
            foreach ($words as $word) {
                $acronym .= strtoupper(substr($word, 0, 1));
            }
            $aliases[] = $acronym;
        }

        // Додаємо альтернативні назви
        if (fake()->boolean(70)) {
            $aliases[] = $name . ' Entertainment';
        }

        if (fake()->boolean(50)) {
            $aliases[] = $name . ' Studios';
        }

        return $aliases;
    }

    /**
     * Генерація API джерел
     */
    protected function generateApiSources(): array
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
     * Створення тегів
     */
    protected function createTags(): void
    {
        $this->command->info('Створення тегів та жанрів...');

        // Список реальних жанрів
        $genres = [
            'Бойовик', 'Комедія', 'Драма', 'Фантастика', 'Жахи',
            'Трилер', 'Романтика', 'Пригоди', 'Анімація', 'Фентезі',
            'Кримінал', 'Документальний', 'Історичний', 'Військовий', 'Вестерн',
            'Мюзикл', 'Спорт', 'Біографія', 'Сімейний', 'Детектив'
        ];

        // Створення жанрів
        foreach (array_slice($genres, 0, $this->genresCount) as $name) {
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $this->generateTagDescription($name, true),
                'image' => $this->getRandomImagePath('genres'),
                'aliases' => $this->generateGenreAliases($name),
                'is_genre' => true,
                'meta_title' => "{$name} фільми та серіали | Netflix",
                'meta_description' => "Дивіться найкращі {$name} фільми та серіали онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('genres_meta'),
            ]);
        }

        // Список реальних тегів
        $tags = [
            'Супергерої', 'Зомбі', 'Вампіри', 'Роботи', 'Постапокаліпсис',
            'Космос', 'Подорожі в часі', 'Магія', 'Бойові мистецтва', 'Війна',
            'Середньовіччя', 'Антиутопія', 'Кіберпанк', 'Стімпанк', 'Паранормальне',
            'Монстри', 'Шпигуни', 'Мафія', 'Поліція', 'Школа',
            'Університет', 'Лікарня', 'Суд', 'Вязниця', 'Армія',
            'Спортивні події', 'Музика', 'Танці', 'Мистецтво', 'Кулінарія'
        ];

        // Створення тегів
        foreach (array_slice($tags, 0, $this->tagsCount) as $name) {
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $this->generateTagDescription($name, false),
                'image' => $this->getRandomImagePath('tags'),
                'aliases' => $this->generateTagAliases($name),
                'is_genre' => false,
                'meta_title' => "{$name} | Netflix",
                'meta_description' => "Фільми та серіали з тегом {$name}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('tags_meta'),
            ]);
        }
    }

    /**
     * Генерація опису для тега
     */
    protected function generateTagDescription(string $name, bool $isGenre): string
    {
        if ($isGenre) {
            $descriptions = [
                "Жанр {$name} характеризується особливою атмосферою та стилем оповіді.",
                "{$name} - один з найпопулярніших жанрів кіно, який має мільйони шанувальників по всьому світу.",
                "Фільми жанру {$name} відрізняються особливим підходом до сюжету та персонажів.",
                "Жанр {$name} має багату історію та традиції в кінематографі.",
                "Стрічки в жанрі {$name} завжди привертають увагу глядачів своєю унікальністю.",
            ];
        } else {
            $descriptions = [
                "Фільми з тегом {$name} об'єднані спільною тематикою та елементами.",
                "{$name} - популярна тема в сучасному кінематографі, яка знаходить відгук у багатьох глядачів.",
                "Стрічки з тегом {$name} часто досліджують специфічні аспекти цієї тематики.",
                "Тег {$name} об'єднує різноманітні фільми, які мають спільні елементи.",
                "Фільми, позначені тегом {$name}, пропонують унікальний погляд на цю тематику.",
            ];
        }

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraph(2);
    }

    /**
     * Генерація альтернативних назв для жанру
     */
    protected function generateGenreAliases(string $name): array
    {
        $aliases = [];

        $genreAliases = [
            'Бойовик' => ['Екшн', 'Action'],
            'Комедія' => ['Comedy'],
            'Драма' => ['Drama'],
            'Фантастика' => ['Sci-Fi', 'Science Fiction'],
            'Жахи' => ['Хоррор', 'Horror'],
            'Трилер' => ['Thriller'],
            'Романтика' => ['Мелодрама', 'Romance'],
            'Пригоди' => ['Adventure'],
            'Анімація' => ['Мультфільм', 'Animation'],
            'Фентезі' => ['Fantasy'],
            'Кримінал' => ['Crime'],
            'Документальний' => ['Documentary'],
            'Історичний' => ['Historical', 'History'],
            'Військовий' => ['War', 'Military'],
            'Вестерн' => ['Western'],
            'Мюзикл' => ['Musical'],
            'Спорт' => ['Sports'],
            'Біографія' => ['Biographical', 'Biography'],
            'Сімейний' => ['Family'],
            'Детектив' => ['Mystery', 'Detective'],
        ];

        if (isset($genreAliases[$name])) {
            $aliases = $genreAliases[$name];
        }

        return $aliases;
    }

    /**
     * Генерація альтернативних назв для тега
     */
    protected function generateTagAliases(string $name): array
    {
        $aliases = [];

        $tagAliases = [
            'Супергерої' => ['Superheroes', 'Heroes'],
            'Зомбі' => ['Zombies', 'Undead'],
            'Вампіри' => ['Vampires', 'Bloodsuckers'],
            'Роботи' => ['Robots', 'AI', 'Artificial Intelligence'],
            'Постапокаліпсис' => ['Post-Apocalyptic', 'Apocalypse'],
            'Космос' => ['Space', 'Outer Space'],
            'Подорожі в часі' => ['Time Travel', 'Timetravel'],
            'Магія' => ['Magic', 'Magical'],
            'Бойові мистецтва' => ['Martial Arts', 'Kung Fu'],
            'Війна' => ['War', 'Warfare'],
            'Середньовіччя' => ['Medieval', 'Middle Ages'],
            'Антиутопія' => ['Dystopia', 'Dystopian'],
            'Кіберпанк' => ['Cyberpunk'],
            'Стімпанк' => ['Steampunk'],
            'Паранормальне' => ['Paranormal', 'Supernatural'],
            'Монстри' => ['Monsters', 'Creatures'],
            'Шпигуни' => ['Spies', 'Espionage'],
            'Мафія' => ['Mafia', 'Gangsters', 'Organized Crime'],
            'Поліція' => ['Police', 'Cops'],
            'Школа' => ['School', 'High School'],
            'Спортивні події' => ['Sporting Events', 'Athletic Events'],
        ];

        if (isset($tagAliases[$name])) {
            $aliases = $tagAliases[$name];
        }

        return $aliases;
    }

    /**
     * Створення персон (акторів, режисерів, тощо)
     */
    protected function createPeople(): void
    {
        $this->command->info('Створення персон...');

        // Список реальних акторів
        $actors = [
            ['name' => 'Том Круз', 'original_name' => 'Tom Cruise', 'gender' => Gender::MALE],
            ['name' => 'Роберт Дауні-молодший', 'original_name' => 'Robert Downey Jr.', 'gender' => Gender::MALE],
            ['name' => 'Скарлетт Йоханссон', 'original_name' => 'Scarlett Johansson', 'gender' => Gender::FEMALE],
            ['name' => 'Дженніфер Лоуренс', 'original_name' => 'Jennifer Lawrence', 'gender' => Gender::FEMALE],
            ['name' => 'Леонардо Ді Капріо', 'original_name' => 'Leonardo DiCaprio', 'gender' => Gender::MALE],
            ['name' => 'Бред Пітт', 'original_name' => 'Brad Pitt', 'gender' => Gender::MALE],
            ['name' => 'Анджеліна Джолі', 'original_name' => 'Angelina Jolie', 'gender' => Gender::FEMALE],
            ['name' => 'Джонні Депп', 'original_name' => 'Johnny Depp', 'gender' => Gender::MALE],
            ['name' => 'Емма Стоун', 'original_name' => 'Emma Stone', 'gender' => Gender::FEMALE],
            ['name' => 'Раян Гослінг', 'original_name' => 'Ryan Gosling', 'gender' => Gender::MALE],
            ['name' => 'Кріс Гемсворт', 'original_name' => 'Chris Hemsworth', 'gender' => Gender::MALE],
            ['name' => 'Галь Гадот', 'original_name' => 'Gal Gadot', 'gender' => Gender::FEMALE],
            ['name' => 'Дуейн Джонсон', 'original_name' => 'Dwayne Johnson', 'gender' => Gender::MALE],
            ['name' => 'Шарліз Терон', 'original_name' => 'Charlize Theron', 'gender' => Gender::FEMALE],
            ['name' => 'Кіану Рівз', 'original_name' => 'Keanu Reeves', 'gender' => Gender::MALE],
            ['name' => 'Марго Роббі', 'original_name' => 'Margot Robbie', 'gender' => Gender::FEMALE],
            ['name' => 'Том Гарді', 'original_name' => 'Tom Hardy', 'gender' => Gender::MALE],
            ['name' => 'Емма Вотсон', 'original_name' => 'Emma Watson', 'gender' => Gender::FEMALE],
            ['name' => 'Ідріс Ельба', 'original_name' => 'Idris Elba', 'gender' => Gender::MALE],
            ['name' => 'Наталі Портман', 'original_name' => 'Natalie Portman', 'gender' => Gender::FEMALE],
        ];

        // Список реальних режисерів
        $directors = [
            ['name' => 'Стівен Спілберг', 'original_name' => 'Steven Spielberg', 'gender' => Gender::MALE],
            ['name' => 'Крістофер Нолан', 'original_name' => 'Christopher Nolan', 'gender' => Gender::MALE],
            ['name' => 'Квентін Тарантіно', 'original_name' => 'Quentin Tarantino', 'gender' => Gender::MALE],
            ['name' => 'Мартін Скорсезе', 'original_name' => 'Martin Scorsese', 'gender' => Gender::MALE],
            ['name' => 'Джеймс Кемерон', 'original_name' => 'James Cameron', 'gender' => Gender::MALE],
            ['name' => 'Софія Коппола', 'original_name' => 'Sofia Coppola', 'gender' => Gender::FEMALE],
            ['name' => 'Гільєрмо дель Торо', 'original_name' => 'Guillermo del Toro', 'gender' => Gender::MALE],
            ['name' => 'Кетрін Бігелоу', 'original_name' => 'Kathryn Bigelow', 'gender' => Gender::FEMALE],
            ['name' => 'Тайка Вайтіті', 'original_name' => 'Taika Waititi', 'gender' => Gender::MALE],
            ['name' => 'Ґрета Ґервіґ', 'original_name' => 'Greta Gerwig', 'gender' => Gender::FEMALE],
        ];

        // Список реальних аніме-персонажів
        $characters = [
            ['name' => 'Наруто Узумакі', 'original_name' => 'Naruto Uzumaki', 'gender' => Gender::MALE],
            ['name' => 'Саске Учіха', 'original_name' => 'Sasuke Uchiha', 'gender' => Gender::MALE],
            ['name' => 'Мікаса Аккерман', 'original_name' => 'Mikasa Ackerman', 'gender' => Gender::FEMALE],
            ['name' => 'Ерен Єгер', 'original_name' => 'Eren Yeager', 'gender' => Gender::MALE],
            ['name' => 'Гоку', 'original_name' => 'Son Goku', 'gender' => Gender::MALE],
            ['name' => 'Сейлор Мун', 'original_name' => 'Sailor Moon', 'gender' => Gender::FEMALE],
            ['name' => 'Спайк Шпігель', 'original_name' => 'Spike Spiegel', 'gender' => Gender::MALE],
            ['name' => 'Леві Аккерман', 'original_name' => 'Levi Ackerman', 'gender' => Gender::MALE],
            ['name' => 'Асука Ленглі', 'original_name' => 'Asuka Langley Soryu', 'gender' => Gender::FEMALE],
            ['name' => 'Едвард Елрік', 'original_name' => 'Edward Elric', 'gender' => Gender::MALE],
        ];

        // Підготовка для унікальних слагів
        $existingSlugs = [];

        // Створення акторів
        foreach ($actors as $actor) {
            $slug = Str::slug($actor['name']);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $slug;
            while (in_array($slug, $existingSlugs)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Add to existing slugs
            $existingSlugs[] = $slug;

            Person::create([
                'name' => $actor['name'],
                'slug' => $slug,
                'original_name' => $actor['original_name'],
                'type' => PersonType::ACTOR,
                'gender' => $actor['gender'],
                'image' => $this->getRandomImagePath('actors'),
                'description' => $this->generatePersonDescription($actor['name'], PersonType::ACTOR),
                'birthday' => fake()->dateTimeBetween('-70 years', '-20 years')->format('Y-m-d'),
                'birthplace' => fake()->city() . ', ' . fake()->country(),
                'meta_title' => $actor['name'] . ' | Netflix',
                'meta_description' => "Фільми та серіали з актором {$actor['name']}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('actors_meta'),
            ]);
        }

        // Створення режисерів
        foreach ($directors as $director) {
            $slug = Str::slug($director['name']);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $slug;
            while (in_array($slug, $existingSlugs)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Add to existing slugs
            $existingSlugs[] = $slug;

            Person::create([
                'name' => $director['name'],
                'slug' => $slug,
                'original_name' => $director['original_name'],
                'type' => PersonType::DIRECTOR,
                'gender' => $director['gender'],
                'image' => $this->getRandomImagePath('directors'),
                'description' => $this->generatePersonDescription($director['name'], PersonType::DIRECTOR),
                'birthday' => fake()->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d'),
                'birthplace' => fake()->city() . ', ' . fake()->country(),
                'meta_title' => $director['name'] . ' | Netflix',
                'meta_description' => "Фільми та серіали режисера {$director['name']}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('directors_meta'),
            ]);
        }

        // Створення аніме-персонажів
        foreach ($characters as $character) {
            $slug = Str::slug($character['name']);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $slug;
            while (in_array($slug, $existingSlugs)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Add to existing slugs
            $existingSlugs[] = $slug;

            Person::create([
                'name' => $character['name'],
                'slug' => $slug,
                'original_name' => $character['original_name'],
                'type' => PersonType::CHARACTER,
                'gender' => $character['gender'],
                'image' => $this->getRandomImagePath('characters'),
                'description' => $this->generatePersonDescription($character['name'], PersonType::CHARACTER),
                'birthday' => fake()->boolean(50) ? fake()->dateTimeBetween('-30 years', '-10 years')->format('Y-m-d') : null,
                'birthplace' => fake()->boolean(50) ? fake()->city() . ', ' . fake()->country() : null,
                'meta_title' => $character['name'] . ' | Netflix',
                'meta_description' => "Аніме з персонажем {$character['name']}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('characters_meta'),
            ]);
        }

        // Створення додаткових персон різних типів
        $personTypes = [
            PersonType::PRODUCER,
            PersonType::WRITER,
            PersonType::COMPOSER,
            PersonType::CINEMATOGRAPHER,
            PersonType::VOICE_ACTOR,
        ];

        $remainingCount = $this->peopleCount - count($actors) - count($directors) - count($characters);

        for ($i = 0; $i < $remainingCount; $i++) {
            $type = $personTypes[array_rand($personTypes)];
            $gender = fake()->randomElement([Gender::MALE, Gender::FEMALE]);
            $firstName = $gender === Gender::MALE ? fake()->firstNameMale() : fake()->firstNameFemale();
            $lastName = fake()->lastName();
            $name = $firstName . ' ' . $lastName;
            $slug = Str::slug($name);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $slug;
            while (in_array($slug, $existingSlugs)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Add to existing slugs
            $existingSlugs[] = $slug;

            Person::create([
                'name' => $name,
                'slug' => $slug,
                'original_name' => $name,
                'type' => $type,
                'gender' => $gender,
                'image' => $this->getRandomImagePath('people'),
                'description' => $this->generatePersonDescription($name, $type),
                'birthday' => fake()->dateTimeBetween('-70 years', '-20 years')->format('Y-m-d'),
                'birthplace' => fake()->city() . ', ' . fake()->country(),
                'meta_title' => $name . ' | Netflix',
                'meta_description' => "Фільми та серіали з участю {$name}. Дивіться онлайн на Netflix.",
                'meta_image' => $this->getRandomImagePath('people_meta'),
            ]);
        }
    }

    /**
     * Генерація опису для персони
     */
    protected function generatePersonDescription(string $name, PersonType $type): string
    {
        $descriptions = [];

        switch ($type) {
            case PersonType::ACTOR:
                $descriptions = [
                    "{$name} - відомий актор, який знявся у багатьох популярних фільмах та серіалах.",
                    "Актор {$name} відомий своєю різноманітністю ролей та глибоким підходом до кожного персонажа.",
                    "{$name} - один з найбільш впізнаваних акторів сучасності, який має мільйони шанувальників.",
                    "Талановитий актор {$name} відомий своєю здатністю перевтілюватися у різноманітних персонажів.",
                    "{$name} - актор, який отримав визнання критиків та глядачів за свої ролі у кіно та на телебаченні.",
                ];
                break;

            case PersonType::DIRECTOR:
                $descriptions = [
                    "{$name} - відомий режисер, який створив безліч культових фільмів.",
                    "Режисер {$name} відомий своїм унікальним стилем та інноваційним підходом до кіновиробництва.",
                    "{$name} - один з найвпливовіших режисерів у світі кіно, чиї роботи отримали міжнародне визнання.",
                    "Талановитий режисер {$name} відомий своїм умінням розкривати глибокі теми через кінематограф.",
                    "{$name} - режисер, який змінив уявлення про кіно своїми новаторськими роботами.",
                ];
                break;

            case PersonType::CHARACTER:
                $descriptions = [
                    "{$name} - популярний персонаж аніме, який став культовим серед шанувальників.",
                    "Персонаж {$name} відомий своїм унікальним характером та розвитком протягом серіалу.",
                    "{$name} - один з найбільш впізнаваних аніме-персонажів, який має мільйони шанувальників.",
                    "Персонаж {$name} став символом жанру та надихнув багатьох творців.",
                    "{$name} - персонаж, який запам'ятався глядачам своєю харизмою та історією.",
                ];
                break;

            default:
                $typeLabel = $type->getLabel() ?? $type->value;
                $descriptions = [
                    "{$name} - відомий {$typeLabel}, який працював над багатьма успішними проектами.",
                    "{$typeLabel} {$name} відомий своїм професіоналізмом та творчим підходом до роботи.",
                    "{$name} - один з найкращих спеціалістів у своїй галузі, чиї роботи отримали визнання.",
                    "Талановитий {$typeLabel} {$name} зробив значний внесок у розвиток кіноіндустрії.",
                    "{$name} - {$typeLabel}, чиї роботи вирізняються високою якістю та оригінальністю.",
                ];
                break;
        }

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraph(2);
    }

    /**
     * Створення фільмів
     */
    protected function createMovies(): void
    {
        $this->command->info('Створення фільмів...');

        // Створення фільмів
        $movies = RealisticDataSeeder\MovieSeeder::createMovies(
            $this->moviesCount,
            [$this, 'getRandomImagePath']
        );

        // Додавання звязків між фільмами
        $this->command->info('Створення звязків між фільмами...');
        RealisticDataSeeder\MovieSeeder::createMovieRelations($movies);

        // Додавання персон до фільмів
        $this->command->info('Додавання персон до фільмів...');
        RealisticDataSeeder\MovieSeeder::addPeopleToMovies($movies);

        // Створення епізодів для серіалів
        $this->command->info('Створення епізодів для серіалів...');
        RealisticDataSeeder\MovieSeeder::createEpisodes(
            $movies,
            [$this, 'getRandomImagePath'],
            $this->episodesPerSeriesMin,
            $this->episodesPerSeriesMax
        );
    }

    /**
     * Створення підбірок
     */
    protected function createSelections(): void
    {
        $this->command->info('Створення підбірок...');

        RealisticDataSeeder\SelectionSeeder::createSelections(
            $this->selectionsCount,
            [$this, 'getRandomImagePath']
        );
    }

    /**
     * Отримання випадкового шляху до зображення
     */
    public function getRandomImagePath(string $category): string
    {
        $path = $this->testImagesPath . '/' . $category;

        // Перевіряємо, чи існує директорія
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);

            // Якщо директорія не існує, повертаємо заглушку
            return "https://picsum.photos/seed/{$category}-" . fake()->numberBetween(1, 1000) . "/800/600";
        }

        // Отримуємо список файлів у директорії
        $files = Storage::files($path);

        // Якщо файлів немає, повертаємо заглушку
        if (empty($files)) {
            return "https://picsum.photos/seed/{$category}-" . fake()->numberBetween(1, 1000) . "/800/600";
        }

        // Вибираємо випадковий файл
        $file = $files[array_rand($files)];

        // Повертаємо шлях до файлу
        return Storage::url($file);
    }
}
