<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieImagesSeeder extends Seeder
{
    /**
     * Запуск сідера для завантаження зображень фільмів
     */
    public function run(): void
    {
        $this->command->info('Запуск сідера для завантаження зображень фільмів...');

        // Перевіряємо наявність директорій для зображень
        $this->ensureDirectoriesExist();

        // Завантажуємо тестові зображення
        $this->downloadTestImages();

        // Оновлюємо зображення для фільмів
        $this->updateMovieImages();

        $this->command->info('Сідер для завантаження зображень фільмів успішно завершив роботу!');
    }

    /**
     * Перевіряє наявність директорій для зображень
     */
    private function ensureDirectoriesExist(): void
    {
        $directories = [
            'movies/posters',
            'movies/images',
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                $this->command->info("Створено директорію: {$directory}");
            }
        }
    }

    /**
     * Завантажує тестові зображення з Unsplash
     */
    private function downloadTestImages(): void
    {
        $posterDirectory = 'movies/posters';
        $imageDirectory = 'movies/images';

        // Перевіряємо, чи вже є зображення
        $existingPosters = Storage::disk('public')->files($posterDirectory);
        $existingImages = Storage::disk('public')->files($imageDirectory);

        if (count($existingPosters) >= 10 && count($existingImages) >= 10) {
            $this->command->info('Знайдено достатньо зображень для фільмів. Пропускаємо завантаження.');
            return;
        }

        // Завантажуємо постери (вертикальні зображення)
        $this->command->info('Завантаження постерів для фільмів...');
        $this->downloadImages(
            'https://api.unsplash.com/photos/random?query=movie+poster&orientation=portrait&count=10',
            $posterDirectory,
            10 - count($existingPosters)
        );

        // Завантажуємо зображення (горизонтальні зображення)
        $this->command->info('Завантаження зображень для фільмів...');
        $this->downloadImages(
            'https://api.unsplash.com/photos/random?query=movie+scene&orientation=landscape&count=10',
            $imageDirectory,
            10 - count($existingImages)
        );
    }

    /**
     * Завантажує реалістичні зображення з API або використовує локальні
     */
    private function downloadImages(string $url, string $directory, int $count): void
    {
        if ($count <= 0) {
            return;
        }

        // Спробуємо завантажити зображення з The Movie Database API
        $this->command->info("Завантаження реалістичних зображень для {$directory}...");

        $isPortrait = $directory === 'movies/posters';
        $imageType = $isPortrait ? 'poster' : 'backdrop';

        try {
            // Використовуємо TMDB API для отримання зображень фільмів
            $apiKey = env('TMDB_API_KEY');

            if (!$apiKey) {
                $this->command->warn('TMDB API ключ не знайдено. Використовуємо альтернативне джерело зображень.');
                $this->downloadFromPexels($directory, $count);
                return;
            }

            // Отримуємо популярні фільми з TMDB
            $response = Http::get("https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=uk-UA");

            if (!$response->successful()) {
                $this->command->warn('Не вдалося отримати дані з TMDB API. Використовуємо альтернативне джерело зображень.');
                $this->downloadFromPexels($directory, $count);
                return;
            }

            $movies = $response->json()['results'] ?? [];
            $downloadedCount = 0;

            foreach ($movies as $movie) {
                if ($downloadedCount >= $count) {
                    break;
                }

                $imagePath = $movie[$imageType . '_path'] ?? null;

                if (!$imagePath) {
                    continue;
                }

                $imageUrl = "https://image.tmdb.org/t/p/" . ($isPortrait ? 'w500' : 'w1280') . $imagePath;
                $filename = Str::uuid() . '.jpg';
                $path = "{$directory}/{$filename}";

                try {
                    $imageContent = Http::get($imageUrl)->body();
                    Storage::disk('public')->put($path, $imageContent);

                    $downloadedCount++;
                    $this->command->info("Завантажено зображення фільму '{$movie['title']}': {$path}");
                } catch (\Exception $e) {
                    $this->command->warn("Не вдалося завантажити зображення для фільму '{$movie['title']}': {$e->getMessage()}");
                }
            }

            // Якщо не вдалося завантажити достатньо зображень, використовуємо альтернативне джерело
            if ($downloadedCount < $count) {
                $this->command->info("Завантажуємо додаткові зображення з альтернативного джерела...");
                $this->downloadFromPexels($directory, $count - $downloadedCount);
            }
        } catch (\Exception $e) {
            $this->command->error("Помилка при завантаженні зображень з TMDB: {$e->getMessage()}");
            $this->downloadFromPexels($directory, $count);
        }
    }

    /**
     * Завантажує зображення з Pexels API або використовує локальні файли
     */
    private function downloadFromPexels(string $directory, int $count): void
    {
        $this->command->info('Завантаження зображень з Pexels...');

        $isPortrait = $directory === 'movies/posters';
        $query = $isPortrait ? 'movie+poster' : 'cinema';
        $orientation = $isPortrait ? 'portrait' : 'landscape';

        try {
            // Використовуємо Pexels API для отримання зображень
            $apiKey = env('PEXELS_API_KEY', '');

            if (!$apiKey) {
                $this->command->warn('Pexels API ключ не знайдено. Використовуємо локальні зображення.');
                $this->useLocalMovieImages($directory, $count);
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->get("https://api.pexels.com/v1/search?query={$query}&orientation={$orientation}&per_page={$count}");

            if (!$response->successful()) {
                $this->command->warn('Не вдалося отримати дані з Pexels API. Використовуємо локальні зображення.');
                $this->useLocalMovieImages($directory, $count);
                return;
            }

            $photos = $response->json()['photos'] ?? [];
            $downloadedCount = 0;

            foreach ($photos as $photo) {
                if ($downloadedCount >= $count) {
                    break;
                }

                $imageUrl = $photo['src'][$isPortrait ? 'large' : 'large2x'] ?? null;

                if (!$imageUrl) {
                    continue;
                }

                $filename = Str::uuid() . '.jpg';
                $path = "{$directory}/{$filename}";

                try {
                    $imageContent = Http::get($imageUrl)->body();
                    Storage::disk('public')->put($path, $imageContent);

                    $downloadedCount++;
                    $this->command->info("Завантажено зображення з Pexels: {$path}");
                } catch (\Exception $e) {
                    $this->command->warn("Не вдалося завантажити зображення з Pexels: {$e->getMessage()}");
                }
            }

            // Якщо не вдалося завантажити достатньо зображень, використовуємо локальні файли
            if ($downloadedCount < $count) {
                $this->command->info("Завантажуємо додаткові зображення з локальних файлів...");
                $this->useLocalMovieImages($directory, $count - $downloadedCount);
            }
        } catch (\Exception $e) {
            $this->command->error("Помилка при завантаженні зображень з Pexels: {$e->getMessage()}");
            $this->useLocalMovieImages($directory, $count);
        }
    }

    /**
     * Використовує локальні зображення фільмів
     */
    private function useLocalMovieImages(string $directory, int $count): void
    {
        $this->command->info('Використання локальних зображень фільмів...');

        $isPortrait = $directory === 'movies/posters';

        // Масив URL зображень фільмів
        $movieImages = $isPortrait ? [
            'https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Ny00ZTQ0LWEyNjctNDVkZjU5YjgyN2UxXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BNzA5ZDNlZWMtM2NhNS00NDJjLTk4NDItYTRmY2EwMWZlMTY3XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BMWU4N2FjNzYtNTVkNC00NzQ0LTg0MjAtYTJlMjFhNGUxZDFmXkEyXkFqcGdeQXVyNjc1NTYyMjg@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BNWIwODRlZTUtY2U3ZS00Yzg1LWJhNzYtMmZiYmEyNmU1NjMzXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BOTY4YjI2N2MtYmFlMC00ZjcyLTg3YjEtMDQyM2ZjYzQ5YWFkXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
        ] : [
            'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba',
            'https://images.unsplash.com/photo-1536440136628-849c177e76a1',
            'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c',
            'https://images.unsplash.com/photo-1440404653325-ab127d49abc1',
            'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0',
            'https://images.unsplash.com/photo-1478720568477-152d9b164e26',
            'https://images.unsplash.com/photo-1542204165-65bf26472b9b',
            'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4',
            'https://images.unsplash.com/photo-1460881680858-30d872d5b530',
            'https://images.unsplash.com/photo-1485846234645-a62644f84728'
        ];

        $downloadedCount = 0;

        // Використовуємо зображення з масиву
        for ($i = 0; $i < $count; $i++) {
            $imageIndex = $i % count($movieImages);
            $imageUrl = $movieImages[$imageIndex];

            $filename = Str::uuid() . '.jpg';
            $path = "{$directory}/{$filename}";

            try {
                $imageContent = Http::get($imageUrl)->body();
                Storage::disk('public')->put($path, $imageContent);

                $downloadedCount++;
                $this->command->info("Завантажено локальне зображення фільму: {$path}");
            } catch (\Exception $e) {
                $this->command->warn("Не вдалося завантажити локальне зображення: {$e->getMessage()}");
                // Якщо не вдалося завантажити зображення, створюємо плейсхолдер
                $this->createPlaceholderImage($directory, $filename, $isPortrait);
            }
        }
    }

    /**
     * Створює плейсхолдер зображення як останній варіант
     */
    private function createPlaceholderImage(string $directory, string $filename, bool $isPortrait): void
    {
        $width = $isPortrait ? 300 : 800;
        $height = $isPortrait ? 450 : 450;
        $path = "{$directory}/{$filename}";

        // Створюємо просте зображення з текстом
        $image = imagecreatetruecolor($width, $height);

        // Випадковий колір фону
        $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($image, 0, 0, $bgColor);

        // Колір тексту
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Додаємо текст
        $text = "Movie Image";
        $fontSize = 5;
        $fontWidth = imagefontwidth($fontSize);
        $fontHeight = imagefontheight($fontSize);
        $textWidth = $fontWidth * strlen($text);
        $textX = ($width - $textWidth) / 2;
        $textY = ($height - $fontHeight) / 2;

        imagestring($image, $fontSize, $textX, $textY, $text, $textColor);

        // Зберігаємо зображення
        ob_start();
        imagejpeg($image);
        $imageContent = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($path, $imageContent);

        $this->command->info("Створено плейсхолдер зображення: {$path}");
    }

    /**
     * Оновлює зображення для фільмів
     */
    private function updateMovieImages(): void
    {
        $movies = Movie::all();
        $this->command->info("Знайдено {$movies->count()} фільмів для оновлення зображень.");

        // Отримуємо всі доступні зображення
        $posters = Storage::disk('public')->files('movies/posters');
        $images = Storage::disk('public')->files('movies/images');

        if (empty($posters) || empty($images)) {
            $this->command->error('Не знайдено зображень для оновлення фільмів.');
            return;
        }

        foreach ($movies as $index => $movie) {
            // Вибираємо випадкові зображення для фільму
            $posterIndex = $index % count($posters);
            $imageIndex = $index % count($images);

            $poster = $posters[$posterIndex];
            $image = $images[$imageIndex];

            // Оновлюємо зображення фільму
            $movie->poster = $poster;
            $movie->image_name = $image;
            $movie->meta_image = $image; // Використовуємо те ж зображення для мета-тегів
            $movie->save();

            $this->command->info("Оновлено зображення для фільму '{$movie->name}'");
        }

        $this->command->info('Зображення для всіх фільмів успішно оновлені!');
    }
}
