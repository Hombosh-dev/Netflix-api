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
            Movie::class => 60, // 60% chance for movies
            Episode::class => 30, // 30% chance for episodes
            Selection::class => 10, // 10% chance for selections
        ];

        $commentableClass = $this->getRandomWeightedElement($commentableClasses);

        $commentable = $commentableClass::query()->inRandomOrder()->first()
            ?? $commentableClass::factory()->create();

        return [
            'commentable_id' => $commentable->id,
            'commentable_type' => $commentableClass,
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'is_spoiler' => fake()->boolean(10), // 10% chance to be a spoiler
            'body' => fake()->paragraph(fake()->numberBetween(1, 3)),
            'parent_id' => null, // By default, comments are root comments
        ];
    }
    
    /**
     * Configure the comment as a spoiler.
     */
    public function asSpoiler(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_spoiler' => true,
        ]);
    }
    
    /**
     * Configure the comment for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
    
    /**
     * Configure the comment for a specific commentable item.
     */
    public function forCommentable(Model $commentable): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);
    }
    
    /**
     * Configure the comment as a reply to another comment.
     */
    public function asReplyTo(Comment $parentComment): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentComment->id,
            'commentable_id' => $parentComment->commentable_id,
            'commentable_type' => $parentComment->commentable_type,
        ]);
    }
    
    /**
     * Backward compatibility method for replyTo
     * @deprecated Use asReplyTo() instead
     */
    public function replyTo(Comment $parentComment): self
    {
        return $this->asReplyTo($parentComment);
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
