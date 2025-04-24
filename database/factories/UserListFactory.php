<?php

namespace Database\Factories;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<UserList>
 */
class UserListFactory extends Factory
{
    public function definition(): array
    {
        // List of available classes for `listable_type`
        $listableClasses = [
            Movie::class => 50, // 50% chance for movies
            Episode::class => 20, // 20% chance for episodes
            Person::class => 15, // 15% chance for persons
            Tag::class => 10, // 10% chance for tags
            Selection::class => 5, // 5% chance for selections
        ];

        // Random selection of class with weighted probabilities
        $listableClass = $this->getRandomWeightedElement($listableClasses);

        // Create or select a random record of the corresponding class
        $listable = $listableClass::inRandomOrder()->first()
            ?? $listableClass::factory()->create();

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'listable_id' => $listable->id,
            'listable_type' => $listableClass,
            'type' => fake()->randomElement(UserListType::cases())->value,
        ];
    }
    
    /**
     * Configure the user list with a specific type.
     */
    public function withType(UserListType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type->value,
        ]);
    }
    
    /**
     * Configure the user list for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
    
    /**
     * Configure the user list for a specific listable item.
     */
    public function forListable(Model $listable): static
    {
        return $this->state(fn (array $attributes) => [
            'listable_id' => $listable->id,
            'listable_type' => get_class($listable),
        ]);
    }
    
    /**
     * Helper method to get a random element with weighted probabilities.
     */
    private function getRandomWeightedElement(array $weightedValues): string
    {
        $rand = mt_rand(1, array_sum($weightedValues));
        
        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
        
        return array_key_first($weightedValues);
    }
}
