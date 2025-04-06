<?php

namespace Database\Factories;

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentReport>
 */
class CommentReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id' => Comment::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'type' => $this->faker->randomElement(CommentReportType::cases()),
            'is_viewed' => $this->faker->boolean(50),
            'body' => $this->faker->optional()->paragraph(),
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
