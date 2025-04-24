<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = 'Password123$';

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => Role::USER->value,
            'avatar' => fake()->optional(0.7)->imageUrl(300, 300, 'people'), // 70% chance to have avatar
            'backdrop' => fake()->optional(0.5)->imageUrl(1920, 1080, 'abstract'), // 50% chance to have backdrop
            'gender' => fake()->boolean(80) ? fake()->randomElement(Gender::cases())->value : null, // 80% chance to have gender
            'description' => fake()->optional(0.6)->text(200), // 60% chance to have description
            'birthday' => fake()->optional(0.7)->dateTimeBetween('-50 years', '-18 years'), // 70% chance to have birthday
            'allow_adult' => fake()->boolean(30), // 30% chance to allow adult content
            'is_banned' => fake()->boolean(5), // 5% chance to be banned
            'last_seen_at' => fake()->optional(0.9)->dateTimeBetween('-1 month', 'now'), // 90% chance to have last seen date
            'is_auto_next' => fake()->boolean(70), // 70% chance to auto-play next episode
            'is_auto_play' => fake()->boolean(60), // 60% chance to auto-play videos
            'is_auto_skip_intro' => fake()->boolean(50), // 50% chance to auto-skip intros
            'is_private_favorites' => fake()->boolean(40), // 40% chance to have private favorites
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the user as an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::ADMIN->value,
        ]);
    }

    /**
     * Configure the user as a moderator.
     */
    public function moderator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::MODERATOR->value,
        ]);
    }

    /**
     * Configure the user as banned.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_banned' => true,
        ]);
    }
}
