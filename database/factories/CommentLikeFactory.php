<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentLike>
 */
class CommentLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comment = Comment::inRandomOrder()->first();

        return [
            'comment_id' => $comment->id,
            'user_id' => User::factory() ?? User::factory(),
            'is_liked' => $this->faker->boolean(),
        ];
    }

    public function liked(): self
    {
        return $this->state(fn () => ['is_liked' => true]);
    }

    public function disliked(): self
    {
        return $this->state(fn () => ['is_liked' => false]);
    }
}
