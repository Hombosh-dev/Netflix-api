<?php

namespace Database\Factories;

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentReport>
 */
class CommentReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment_id' => Comment::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'type' => fake()->randomElement(CommentReportType::cases()),
            'is_viewed' => fake()->boolean(50),
            'body' => fake()->optional()->paragraph(),
        ];
    }

    public function forCommentAndUser(Comment $comment, User $user): self
    {
        return $this->state([
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }

    public function withType(CommentReportType $type): self
    {
        return $this->state([
            'type' => $type->value,
        ]);
    }
}
