<?php

namespace Database\Seeders;

use App\Enums\Kind;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use App\Models\Movie;
use App\ValueObjects\VideoPlayer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EpisodeSeeder extends Seeder
{
    // Масив реалістичних назв епізодів для різних жанрів
    protected $episodeTitles = [
        // Драма
        'drama' => [
            'Початок кінця', 'Темна ніч', 'Останній шанс', 'Нова надія', 'Зустріч',
            'Прощання', 'Повернення додому', 'Втрачені спогади', 'Шлях до істини', 'Відкриття',
            'Зрада', 'Примирення', 'Таємниця минулого', 'Вибір', 'Перехрестя',
            'Сповідь', 'Розплата', 'Спокута', 'Нове життя', 'Відродження',
        ],
        // Комедія
        'comedy' => [
            'Той випадок з...', 'Несподіваний поворот', 'Вечірка', 'Непорозуміння', 'Сюрприз',
            'День навпаки', 'Великий план', 'Помилка', 'Побачення наосліп', 'Конкуренція',
            'Парі', 'Родинна вечеря', 'Відпустка', 'Нова робота', 'Переїзд',
            'Возз\'єднання', 'Святкування', 'Сусіди', 'Подарунок', 'Розіграш',
        ],
        // Фантастика
        'scifi' => [
            'Паралельні світи', 'Контакт', 'Вторгнення', 'Аномалія', 'Портал',
            'Подорож у часі', 'Експеримент', 'Штучний інтелект', 'Нова планета', 'Перший контакт',
            'Мутація', 'Колонія', 'Останній корабель', 'Сигнал', 'Симуляція',
            'Пробудження', 'Нова ера', 'Зоряна брама', 'Протокол', 'Карантин',
        ],
        // Фентезі
        'fantasy' => [
            'Пророцтво', 'Артефакт', 'Магічний ритуал', 'Древнє зло', 'Обраний',
            'Чарівний ліс', 'Дракони', 'Заклинання', 'Темний лорд', 'Магічна школа',
            'Перевертні', 'Вампіри', 'Чаклунство', 'Легенда', 'Підземелля',
            'Магічна битва', 'Зачарований замок', 'Прокляття', 'Чарівний меч', 'Безсмертя',
        ],
        // Аніме
        'anime' => [
            'Тренування', 'Турнір', 'Нова сила', 'Перетворення', 'Фінальна битва',
            'Шкільний фестиваль', 'Літні канікули', 'Культурний фестиваль', 'Пляжний епізод', 'Гарячі джерела',
            'Різдвяний епізод', 'Новий учень', 'Спортивний день', 'Екзамени', 'Випускний',
            'Зізнання', 'Фестиваль феєрверків', 'Тренувальний табір', 'Дуель', 'Таємниця розкрита',
        ],
    ];

    // Масив реалістичних описів епізодів
    protected $episodeDescriptions = [
        // Драма
        'drama' => [
            'Головний герой стикається з важким вибором, який може змінити все його життя.',
            'Таємниці минулого починають розкриватися, змінюючи уявлення героїв про себе та оточуючих.',
            'Несподівана зустріч змушує героя переосмислити свої цінності та пріоритети.',
            'Конфлікт між головними героями досягає свого піку, загрожуючи зруйнувати їхні стосунки назавжди.',
            'Трагічна подія об\'єднує героїв перед лицем спільної загрози.',
            'Герой змушений зіткнутися зі своїми найбільшими страхами, щоб захистити тих, кого любить.',
            'Несподіване зізнання змінює динаміку стосунків між головними героями.',
            'Минуле наздоганяє героя, змушуючи його відповідати за свої вчинки.',
        ],
        // Комедія
        'comedy' => [
            'Серія непорозумінь призводить до комічної ситуації, з якої героям доводиться вибиратися.',
            'Спроба справити враження обертається катастрофою з неочікуваними наслідками.',
            'Герої опиняються в незручній соціальній ситуації, намагаючись зберегти обличчя.',
            'Спонтанна вечірка виходить з-під контролю, створюючи хаос і веселощі.',
            'Герої змагаються між собою, що призводить до смішних і абсурдних ситуацій.',
            'Спроба приховати правду призводить до ще більшої брехні та комічних ситуацій.',
            'Герої міняються ролями, що призводить до несподіваних відкриттів про себе та інших.',
            'Невдалий розіграш обертається проти самих жартівників.',
        ],
        // Фантастика
        'scifi' => [
            'Дивна аномалія з\'являється в місті, змушуючи героїв досліджувати її походження.',
            'Експеримент з новою технологією призводить до непередбачуваних наслідків.',
            'Контакт з невідомою формою життя змінює розуміння героїв про всесвіт.',
            'Герої виявляють, що реальність не така, якою вони її вважали.',
            'Подорож у часі створює парадокс, який загрожує існуванню самого часу.',
            'Штучний інтелект починає проявляти ознаки самосвідомості, ставлячи етичні питання.',
            'Герої опиняються в паралельному вимірі, шукаючи шлях додому.',
            'Нова технологія потрапляє не в ті руки, створюючи загрозу для всього людства.',
        ],
        // Фентезі
        'fantasy' => [
            'Древнє пророцтво починає збуватися, змінюючи долю головного героя.',
            'Магічний артефакт з неймовірною силою потрапляє до рук героїв.',
            'Темні сили пробуджуються після століть сну, загрожуючи світу.',
            'Герої вирушають у небезпечну подорож до забороненої землі.',
            'Магічний ритуал відкриває портал в інший світ, повний небезпек і чудес.',
            'Герой відкриває в собі приховані магічні здібності, які змінюють його долю.',
            'Древнє прокляття падає на королівство, і тільки герої можуть його зняти.',
            'Битва між силами світла і темряви визначає долю всього світу.',
        ],
        // Аніме
        'anime' => [
            'Головний герой проходить інтенсивне тренування, щоб опанувати нову техніку.',
            'Починається турнір, де герої змагаються, демонструючи свої навички.',
            'Шкільний фестиваль стає місцем неочікуваних зізнань і розкриття почуттів.',
            'Герой зустрічає сильного супротивника, який змушує його перевершити свої межі.',
            'Під час літніх канікул герої зближуються, розкриваючи нові сторони своїх характерів.',
            'Таємниця минулого головного героя розкривається, змінюючи його стосунки з іншими.',
            'Новий трансфер-учень з\'являється в школі, приносячи з собою таємниці та проблеми.',
            'Герої вирушають у тренувальний табір, де стикаються з несподіваними випробуваннями.',
        ],
    ];

    // Масив реалістичних дубляжів
    protected $dubbingOptions = [
        'Українська студія дубляжу',
        'Багатоголосий закадровий',
        'Дводубляж',
        'Оригінал з субтитрами',
        'Дубляж студії 1+1',
        'Дубляж студії Так Треба Продакшн',
        'Дубляж студії Postmodern',
        'Дубляж студії Pie Post Production',
        'Дубляж студії LeDoyen',
        'Дубляж студії Tretyakoff Production',
    ];

    /**
     * Запуск сідера
     */
    public function run(): void
    {
        $this->command->info('Запуск універсального сідера епізодів...');

        // Перевіряємо наявність директорій для зображень
        $this->ensureDirectoriesExist();

        // Завантажуємо тестові зображення, якщо їх немає
        $this->downloadTestImages();

        // 1. Додаємо епізоди до фільмів (по 1 епізоду)
        $this->seedMovieEpisodes();

        // 2. Додаємо епізоди до серіалів (мінімум 12 епізодів)
        $this->seedTvSeriesEpisodes();

        // 3. Додаємо епізоди до нових серіалів
        $this->seedNewSeriesEpisodes();

        $this->command->info('Універсальний сідер епізодів успішно завершив роботу!');
    }

    /**
     * Додавання епізодів до фільмів (по 1 епізоду)
     */
    protected function seedMovieEpisodes(): void
    {
        $this->command->info('Перевірка та додавання епізодів до фільмів...');

        // Отримуємо всі фільми (не серіали)
        $movies = Movie::whereIn('kind', [Kind::MOVIE, Kind::ANIMATED_MOVIE])->get();

        if ($movies->isEmpty()) {
            $this->command->info('Фільмів не знайдено.');
            return;
        }

        $this->command->info('Знайдено ' . $movies->count() . ' фільмів.');

        // Для кожного фільму перевіряємо наявність епізоду
        foreach ($movies as $movie) {
            $episodesCount = $movie->episodes()->count();

            if ($episodesCount === 0) {
                $this->command->info("Фільм '{$movie->name}' не має епізодів. Додавання епізоду...");
                $this->createEpisodeForMovie($movie);
            } elseif ($episodesCount === 1) {
                $this->command->info("Фільм '{$movie->name}' вже має 1 епізод. Оновлення зображень...");
                $this->updateEpisodeImages($movie);
            } else {
                $this->command->warn("Фільм '{$movie->name}' має {$episodesCount} епізодів. Видалення зайвих епізодів...");
                // Залишаємо тільки перший епізод
                $firstEpisode = $movie->episodes()->orderBy('number')->first();
                $movie->episodes()->where('id', '!=', $firstEpisode->id)->delete();
                $this->updateEpisodeImages($movie);
            }
        }

        $this->command->info('Епізоди для фільмів успішно оновлені!');
    }

    /**
     * Додавання епізодів до серіалів (мінімум 12 епізодів)
     */
    protected function seedTvSeriesEpisodes(): void
    {
        $this->command->info('Балансування кількості епізодів у серіалах...');

        // Отримуємо всі серіали з кількістю епізодів
        $series = Movie::whereIn('kind', [Kind::TV_SERIES, Kind::ANIMATED_SERIES])
            ->withCount('episodes')
            ->get()
            ->map(function ($movie) {
                return [
                    'model' => $movie,
                    'episodes_count' => $movie->episodes_count
                ];
            })
            ->sortBy('episodes_count');

        if ($series->isEmpty()) {
            $this->command->info('Серіалів не знайдено.');
            return;
        }

        // Визначаємо цільову кількість епізодів (мінімум 12)
        $targetEpisodes = 12;

        $this->command->info("Цільова кількість епізодів: {$targetEpisodes}");

        // Додаємо епізоди до серіалів з кількістю епізодів менше цільової
        foreach ($series as $item) {
            $movie = $item['model'];
            $currentCount = $item['episodes_count'];

            if ($currentCount < $targetEpisodes) {
                $episodesToAdd = $targetEpisodes - $currentCount;
                $this->command->info("Серіал '{$movie->name}' має {$currentCount} епізодів. Додавання {$episodesToAdd} епізодів...");

                // Отримуємо існуючі номери епізодів
                $existingNumbers = $movie->episodes()->pluck('number')->toArray();
                $maxNumber = !empty($existingNumbers) ? max($existingNumbers) : 0;

                // Визначаємо жанр серіалу
                $genre = $this->determineGenre($movie);

                // Додаємо епізоди
                for ($i = 1; $i <= $episodesToAdd; $i++) {
                    $number = $maxNumber + $i;
                    $this->createEpisode($movie, $number, $genre);
                }
            } else {
                $this->command->info("Серіал '{$movie->name}' вже має {$currentCount} епізодів (>= {$targetEpisodes}). Оновлення зображень...");
                $this->updateSeriesEpisodeImages($movie);
            }
        }

        $this->command->info('Балансування епізодів успішно завершено!');
    }

    /**
     * Додавання епізодів до нових серіалів
     */
    protected function seedNewSeriesEpisodes(): void
    {
        $this->command->info('Пошук нових серіалів без епізодів...');

        // Знаходимо серіали без епізодів
        $newSeries = Movie::whereIn('kind', [Kind::TV_SERIES, Kind::ANIMATED_SERIES])
            ->whereDoesntHave('episodes')
            ->get();

        if ($newSeries->isEmpty()) {
            $this->command->info('Нових серіалів без епізодів не знайдено.');
            return;
        }

        $this->command->info('Знайдено ' . $newSeries->count() . ' нових серіалів без епізодів.');

        // Цільова кількість епізодів для нових серіалів
        $targetEpisodes = 12;

        foreach ($newSeries as $series) {
            $this->command->info("Додавання епізодів до нового серіалу '{$series->name}'...");

            // Визначаємо жанр серіалу
            $genre = $this->determineGenre($series);

            // Додаємо епізоди
            for ($i = 1; $i <= $targetEpisodes; $i++) {
                $this->createEpisode($series, $i, $genre);
            }
        }

        $this->command->info('Епізоди успішно додані до всіх нових серіалів!');
    }

    /**
     * Створення епізоду для фільму
     */
    protected function createEpisodeForMovie(Movie $movie): void
    {
        // Генеруємо зображення для епізоду
        $pictures = $this->generatePictures();

        // Створюємо епізод
        Episode::create([
            'movie_id' => $movie->id,
            'number' => 1,
            'name' => $movie->name,
            'slug' => Str::slug($movie->name . '-episode-1'),
            'description' => "Повнометражний фільм {$movie->name}. " . $movie->description,
            'duration' => $movie->duration,
            'air_date' => $movie->first_air_date,
            'is_filler' => false,
            'pictures' => $pictures,
            'video_players' => $this->generateVideoPlayers(),
            'meta_title' => "{$movie->name} | Netflix",
            'meta_description' => "Дивіться фільм {$movie->name} онлайн на Netflix.",
        ]);
    }

    /**
     * Оновлення зображень для епізоду фільму
     */
    protected function updateEpisodeImages(Movie $movie): void
    {
        $episode = $movie->episodes()->first();
        if ($episode) {
            $pictures = $this->generatePictures();
            $episode->update([
                'pictures' => $pictures,
            ]);
        }
    }

    /**
     * Оновлення зображень для епізодів серіалу
     */
    protected function updateSeriesEpisodeImages(Movie $series): void
    {
        $episodes = $series->episodes()->get();
        foreach ($episodes as $episode) {
            $pictures = $this->generatePictures();
            $episode->update([
                'pictures' => $pictures,
            ]);
        }
    }

    /**
     * Створення епізоду для серіалу
     */
    protected function createEpisode(Movie $show, int $number, string $genre): void
    {
        // Вибираємо випадкову назву з відповідного жанру
        $titles = $this->episodeTitles[$genre];
        $title = $titles[array_rand($titles)];

        // Додаємо номер до назви
        $episodeName = "Епізод {$number}: {$title}";

        // Вибираємо випадковий опис з відповідного жанру
        $descriptions = $this->episodeDescriptions[$genre];
        $description = $descriptions[array_rand($descriptions)];

        // Додаємо деталі про епізод
        $fullDescription = $description . "\n\n" . $this->generateAdditionalDescription($show->name, $number);

        // Створюємо епізод
        Episode::create([
            'movie_id' => $show->id,
            'number' => $number,
            'name' => $episodeName,
            'slug' => Str::slug($show->name . '-episode-' . $number),
            'description' => $fullDescription,
            'duration' => rand(20, 60),
            'air_date' => $this->generateAirDate($show, $number),
            'is_filler' => rand(1, 10) === 1, // 10% шанс бути філлером
            'pictures' => $this->generatePictures(),
            'video_players' => $this->generateVideoPlayers(),
            'meta_title' => "{$episodeName} | {$show->name} | Netflix",
            'meta_description' => "Дивіться {$episodeName} серіалу {$show->name} онлайн на Netflix.",
        ]);
    }

    /**
     * Визначення жанру серіалу
     */
    protected function determineGenre(Movie $show): string
    {
        // Спробуємо визначити жанр за тегами
        $genreTags = $show->tags()->where('is_genre', true)->pluck('name')->toArray();

        if (!empty($genreTags)) {
            $genreName = strtolower($genreTags[0]);

            if (str_contains($genreName, 'драм')) return 'drama';
            if (str_contains($genreName, 'комед')) return 'comedy';
            if (str_contains($genreName, 'фантаст')) return 'scifi';
            if (str_contains($genreName, 'фентез')) return 'fantasy';
            if (str_contains($genreName, 'аніме') || $show->kind === Kind::ANIMATED_SERIES) return 'anime';
        }

        // За замовчуванням
        if ($show->kind === Kind::ANIMATED_SERIES) return 'anime';

        // Випадковий жанр, якщо не вдалося визначити
        return array_rand(array_flip(['drama', 'comedy', 'scifi', 'fantasy']));
    }

    /**
     * Генерація додаткового опису для епізоду
     */
    protected function generateAdditionalDescription(string $showName, int $number): string
    {
        $descriptions = [
            "У цьому епізоді серіалу {$showName} глядачі побачать неочікуваний розвиток подій, який змінить хід всього сюжету.",
            "Епізод {$number} розкриває нові таємниці та повороти сюжету, які тримають глядачів у напрузі до самого кінця.",
            "Цей епізод серіалу {$showName} є одним з ключових у сезоні, розкриваючи важливі деталі історії головних героїв.",
            "Напружений епізод, який не залишить байдужим жодного глядача серіалу {$showName}.",
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Генерація дати виходу епізоду
     */
    protected function generateAirDate(Movie $show, int $number): string
    {
        // Базова дата - дата першого показу серіалу
        $baseDate = $show->first_air_date;

        // Додаємо від 7 до 14 днів за кожен епізод
        $daysToAdd = ($number - 1) * rand(7, 14);

        return date('Y-m-d', strtotime($baseDate . " + {$daysToAdd} days"));
    }

    /**
     * Генерація зображень для епізоду
     */
    protected function generatePictures(): array
    {
        $pictures = [];
        $count = rand(2, 4);

        $files = Storage::disk('public')->files('episodes');
        $imageFiles = array_filter($files, function($file) {
            return Str::endsWith($file, ['.jpg', '.jpeg', '.png']);
        });

        // Якщо є зображення, використовуємо їх
        if (!empty($imageFiles)) {
            // Вибираємо випадкові зображення
            $randomKeys = array_rand($imageFiles, min($count, count($imageFiles)));
            if (!is_array($randomKeys)) {
                $randomKeys = [$randomKeys];
            }

            foreach ($randomKeys as $key) {
                $pictures[] = $imageFiles[$key];
            }
        } else {
            // Якщо зображень немає, створюємо заглушки
            for ($i = 1; $i <= $count; $i++) {
                $pictures[] = "episodes/episode_" . rand(1, 10) . ".jpg";
            }
        }

        return $pictures;
    }

    /**
     * Перевірка наявності директорій для зображень
     */
    protected function ensureDirectoriesExist(): void
    {
        $directories = [
            'episodes',
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                $this->command->info("Створено директорію: {$directory}");
            }
        }
    }

    /**
     * Завантаження тестових зображень
     */
    protected function downloadTestImages(): void
    {
        $directory = 'episodes';

        // Створюємо директорію, якщо вона не існує
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
            $this->command->info("Створено директорію: {$directory}");
        }

        $files = Storage::disk('public')->files($directory);
        $imageFiles = array_filter($files, function($file) {
            return Str::endsWith($file, ['.jpg', '.jpeg', '.png']);
        });

        // Якщо вже є зображення, не завантажуємо нові
        if (!empty($imageFiles)) {
            $this->command->info("Знайдено " . count($imageFiles) . " зображень для епізодів.");
            return;
        }

        $this->command->info("Завантаження тестових зображень для епізодів...");

        // Список URL зображень з IMDb та інших джерел
        $imageUrls = [
            'https://m.media-amazon.com/images/M/MV5BYjFkMTlkYWUtZWFhNy00M2FmLThiOTYtYTRiYjVlZWYxNmJkXkEyXkFqcGdeQXVyNTAyODkwOQ@@._V1_.jpg', // Breaking Bad
            'https://m.media-amazon.com/images/M/MV5BZjRjOTFkOTktZWUzMi00YzMyLThkMmYtMjEwNmQyNzliYTNmXkEyXkFqcGdeQXVyNzQ1ODk3MTQ@._V1_.jpg', // Rick and Morty
            'https://m.media-amazon.com/images/M/MV5BMTc5ZmM0OTQtNDY4MS00ZjMyLTgwYzgtOGY0Y2VlMWFmNDU0XkEyXkFqcGdeQXVyNDIzMzcwNjc@._V1_.jpg', // Game of Thrones
            'https://m.media-amazon.com/images/M/MV5BMDZkYmVhNjMtNWU4MC00MDQxLWE3MjYtZGMzZWI1ZjhlOWJmXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_.jpg', // Stranger Things
            'https://m.media-amazon.com/images/M/MV5BYWE3MDVkN2EtNjQ5MS00ZDQ4LTliNzYtMjc2YWMzMDEwMTA3XkEyXkFqcGdeQXVyMTEzMTI1Mjk3._V1_.jpg', // Squid Game
            'https://m.media-amazon.com/images/M/MV5BMjhiMzgxZTctNDc1Ni00OTIxLTlhMTYtZTA3ZWFkODRkNmE2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg', // Breaking Bad 2
            'https://m.media-amazon.com/images/M/MV5BOGE4MmVjMDgtMzIzYy00NjEwLWJlODMtMDI1MGY2ZDlhMzE2XkEyXkFqcGdeQXVyMzY0MTE3NzU@._V1_.jpg', // Chernobyl
            'https://m.media-amazon.com/images/M/MV5BMTRmYzNmOTctZjMwOS00ODZlLWJiZGQtNDg5NDY5NjE3MTczXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg', // Peaky Blinders
            'https://m.media-amazon.com/images/M/MV5BODk4ZjU0NDUtYjdlOS00OTljLTgwZTUtYjkyZjk1NzExZGU3XkEyXkFqcGdeQXVyMDM2NDM2MQ@@._V1_.jpg', // The Mandalorian
            'https://m.media-amazon.com/images/M/MV5BZmY5ZDMxODEtNWIwOS00NjdkLTkyMjktNWRjMDhmYjJjN2RmXkEyXkFqcGdeQXVyNTA4NzY1MzY@._V1_.jpg', // Friends
        ];

        // Завантажуємо зображення
        foreach ($imageUrls as $i => $url) {
            $filename = "episode_" . ($i + 1) . ".jpg";
            $path = "{$directory}/{$filename}";

            try {
                $this->command->info("Завантаження зображення з {$url}");
                $contents = @file_get_contents($url);

                if ($contents) {
                    Storage::disk('public')->put($directory . '/' . $filename, $contents);
                    $this->command->info("Завантажено зображення: {$filename}");
                } else {
                    $this->command->error("Не вдалося завантажити зображення з {$url}");
                    // Створюємо резервне зображення
                    $this->createBackupImage($directory . '/' . $filename, "Episode " . ($i + 1));
                }
            } catch (\Exception $e) {
                $this->command->error("Помилка завантаження зображення: " . $e->getMessage());
                // Створюємо резервне зображення
                $this->createBackupImage($directory . '/' . $filename, "Episode " . ($i + 1));
            }
        }
    }

    /**
     * Створення резервного зображення з текстом
     */
    protected function createBackupImage(string $path, string $text): void
    {
        // Перевіряємо, чи доступна бібліотека GD
        if (!extension_loaded('gd')) {
            $this->command->error("Бібліотека GD не доступна. Не вдалося створити резервне зображення.");
            return;
        }

        // Розміри зображення
        $width = 800;
        $height = 450;

        // Створюємо зображення
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            $this->command->error("Не вдалося створити резервне зображення.");
            return;
        }

        // Кольори
        $backgroundColor = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Заповнюємо фон
        imagefill($image, 0, 0, $backgroundColor);

        // Додаємо текст
        $fontSize = 5;
        $fontFile = 5; // Використовуємо вбудований шрифт

        // Центруємо текст
        $textWidth = imagefontwidth($fontFile) * strlen($text);
        $textHeight = imagefontheight($fontFile);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        // Додаємо текст
        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        // Зберігаємо зображення
        ob_start();
        imagejpeg($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($path, $contents);
        $this->command->info("Створено резервне зображення: " . basename($path));
    }

    /**
     * Генерація відеоплеєрів для епізоду
     */
    protected function generateVideoPlayers(): array
    {
        $players = [];
        $playerCount = rand(1, 3);

        for ($i = 0; $i < $playerCount; $i++) {
            $playerName = VideoPlayerName::cases()[array_rand(VideoPlayerName::cases())];
            $quality = VideoQuality::cases()[array_rand(VideoQuality::cases())];
            $dubbing = $this->dubbingOptions[array_rand($this->dubbingOptions)];

            $players[] = new VideoPlayer(
                name: $playerName,
                url: "https://example.com/watch?v=" . Str::random(11),
                file_url: "https://example.com/videos/" . Str::random(8) . ".mp4",
                dubbing: $dubbing,
                quality: $quality,
                locale_code: 'uk'
            );
        }

        return $players;
    }
}
