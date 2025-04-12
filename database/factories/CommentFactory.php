<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        $commentableClasses = [
            Movie::class,
            Episode::class,
            Selection::class,
        ];

        $commentableClass = fake()->randomElement($commentableClasses);

        $commentable = $commentableClass::query()->inRandomOrder()->first()
            ?? $commentableClass::factory()->create();

        return [
            'commentable_id' => $commentable->id,
            'commentable_type' => $commentableClass,
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'is_spoiler' => fake()->boolean(10), // 10% ймовірність, що це спойлер
            'body' => fake()->paragraph(),
        ];
    }

    /**
     * Встановлює батьківський коментар (для вкладених коментарів).
     */
    public function withParent(Comment $parent): self
    {
        return $this->state(fn () => ['parent_id' => $parent->id]);
    }

    /**
     * Для коментаря, який є кореневим.
     */
    public function root(): self
    {
        return $this->state(fn () => ['parent_id' => null]);
    }

    /**
     * Встановлює коментар як відповідь на інший коментар.
     */
    public function replyTo(Comment $parentComment): self
    {
        return $this->state(fn () => [
            'parent_id' => $parentComment->id,
            'commentable_id' => $parentComment->commentable_id,
            'commentable_type' => $parentComment->commentable_type,
        ]);
    }

    /**
     * Встановлює поліморфний зв'язок з вказаним типом і ID.
     */
    public function forCommentable(Model $commentable): self
    {
        return $this->state(fn () => [
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);
    }

    /**
     * Встановлює користувача, який залишив коментар.
     */
    public function forUser(User $user): self
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}
