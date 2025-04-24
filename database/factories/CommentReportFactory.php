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
        $comment = Comment::inRandomOrder()->first() ?? Comment::factory()->create();

        return [
            'comment_id' => $comment->id,
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'type' => fake()->randomElement(CommentReportType::cases()),
            'body' => fake()->optional(0.7)->paragraph(), // 70% chance to have a description
            'is_viewed' => fake()->boolean(30), // 30% chance to be viewed by moderator
        ];
    }

    /**
     * Configure the report as viewed by a moderator.
     */
    public function viewed(): self
    {
        return $this->state(fn () => ['is_viewed' => true]);
    }

    /**
     * Configure the report with a specific report type.
     */
    public function withType(CommentReportType $type): self
    {
        return $this->state(fn () => ['type' => $type]);
    }

    /**
     * Configure the report for a specific comment and user.
     */
    public function forCommentAndUser(Comment $comment, User $user): self
    {
        return $this->state(fn () => [
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }
}
