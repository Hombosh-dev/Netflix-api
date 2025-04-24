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
        $comment = Comment::inRandomOrder()->first() ?? Comment::factory()->create();

        return [
            'comment_id' => $comment->id,
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'is_liked' => fake()->boolean(), // 50% chance for like, 50% for dislike
        ];
    }

    /**
     * Configure the comment like as a positive like.
     */
    public function liked(): self
    {
        return $this->state(fn () => ['is_liked' => true]);
    }

    /**
     * Configure the comment like as a dislike.
     */
    public function disliked(): self
    {
        return $this->state(fn () => ['is_liked' => false]);
    }
    
    /**
     * Configure the comment like for a specific comment and user.
     */
    public function forCommentAndUser(Comment $comment, User $user): self
    {
        return $this->state([
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }
}
