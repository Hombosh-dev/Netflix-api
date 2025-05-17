<?php

namespace Database\Seeders\RealisticDataSeeder;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SelectionSeeder
{
    // Реальні назви підбірок
    protected static $selectionNames = [
        'Найкращі фільми року',
        'Топ-10 комедій',
        'Фільми для сімейного перегляду',
        'Найкращі серіали всіх часів',
        'Фільми, засновані на реальних подіях',
        'Мультфільми для дітей',
        'Фільми про супергероїв',
        'Романтичні комедії',
        'Фільми жахів',
        'Науково-фантастичні фільми',
        'Фільми про космос',
        'Історичні драми',
        'Фільми про війну',
        'Документальні фільми',
        'Аніме для початківців',
        'Фільми, які змінили кінематограф',
        'Фільми з несподіваним фіналом',
        'Фільми про подорожі в часі',
        'Фільми про штучний інтелект',
        'Фільми про зомбі',
    ];

    /**
     * Створення підбірок
     */
    public static function createSelections(int $count, callable $getRandomImagePath): void
    {
        $movies = Movie::all();
        $persons = Person::all();

        // Перевіряємо, чи є користувачі в системі
        $users = User::all();

        // Якщо користувачів немає, створюємо одного
        if ($users->isEmpty()) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $users = User::all();
        }

        // Перемішуємо та обмежуємо кількість назв підбірок
        $selectionNames = self::$selectionNames;
        shuffle($selectionNames);
        $selectionNames = array_slice($selectionNames, 0, $count);

        foreach ($selectionNames as $name) {
            // Вибір випадкового користувача як автора підбірки
            $user = $users->random();

            // Створення підбірки
            $selection = Selection::create([
                'user_id' => $user->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => self::generateSelectionDescription($name),
                'meta_title' => $name . ' | Netflix',
                'meta_description' => "Підбірка {$name}. Дивіться онлайн на Netflix.",
                'meta_image' => $getRandomImagePath('selections_meta'),
                'is_published' => fake()->boolean(80),
            ]);

            // Додавання фільмів до підбірки
            $movieCount = fake()->numberBetween(5, 15);
            $selectionMovies = $movies->random(min($movieCount, $movies->count()));

            foreach ($selectionMovies as $movie) {
                $selection->movies()->attach($movie->id);
            }

            // Додавання персон до підбірки
            if (fake()->boolean(50)) {
                $personCount = fake()->numberBetween(3, 8);
                $selectionPersons = $persons->random(min($personCount, $persons->count()));

                foreach ($selectionPersons as $person) {
                    $selection->persons()->attach($person->id);
                }
            }
        }
    }

    /**
     * Генерація опису для підбірки
     */
    protected static function generateSelectionDescription(string $name): string
    {
        $descriptions = [
            "Підбірка \"{$name}\" містить найкращі фільми та серіали, які варто подивитися.",
            "У цій підбірці \"{$name}\" ви знайдете фільми та серіали, які не залишать вас байдужими.",
            "Підбірка \"{$name}\" створена спеціально для шанувальників якісного кіно.",
            "У \"{$name}\" зібрані найцікавіші та найяскравіші представники жанру.",
            "Підбірка \"{$name}\" допоможе вам знайти ідеальний фільм для перегляду.",
        ];

        return $descriptions[array_rand($descriptions)] . ' ' . fake()->paragraphs(2, true);
    }
}
