<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentLike>
 */
class CommentLikeFactory extends Factory
{
    public function definition(): array
    {
        $comment = Comment::inRandomOrder()->first();

        return [
            'comment_id' => $comment->id,
            'user_id' => User::factory() ?? User::factory(),
            'is_liked' => fake()->boolean(),
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
