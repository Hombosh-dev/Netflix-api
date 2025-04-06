<?php

namespace Database\Factories;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\People;
use App\Models\Selection;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserList>
 */
class UserListsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Список доступних класів для `listable_type`
        $listableClasses = [
            Movie::class,
            Episode::class,
            People::class,
            Tags::class,
            Selection::class,
        ];

        // Випадковий вибір класу
        $listableClass = $this->faker->randomElement($listableClasses);

        // Створення або вибір випадкового запису відповідного класу
        $listable = $listableClass::inRandomOrder()->first()
            ?? $listableClass::factory()->create();

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'listable_id' => $listable->id,
            'listable_type' => $listableClass,
            'type' => $this->faker->randomElement(UserListType::cases())->value,
        ];
    }
}
