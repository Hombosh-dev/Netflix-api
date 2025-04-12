<?php

namespace Database\Factories;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\People;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserList>
 */
class UserListFactory extends Factory
{
    public function definition(): array
    {
        // Список доступних класів для `listable_type`
        $listableClasses = [
            Movie::class,
            Episode::class,
            Person::class,
            Tag::class,
            Selection::class,
        ];

        // Випадковий вибір класу
        $listableClass = fake()->randomElement($listableClasses);

        // Створення або вибір випадкового запису відповідного класу
        $listable = $listableClass::inRandomOrder()->first()
            ?? $listableClass::factory()->create();

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'listable_id' => $listable->id,
            'listable_type' => $listableClass,
            'type' => fake()->randomElement(UserListType::cases())->value,
        ];
    }
}
