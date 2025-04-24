<?php

namespace App\DTOs\Ratings;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class RatingStoreDTO extends BaseDTO
{
    /**
     * Create a new RatingStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $movieId Movie ID
     * @param int $number Rating number (1-10)
     * @param string|null $review Review text
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $movieId,
        public readonly int $number,
        public readonly ?string $review = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'user_id' => 'userId',
            'movie_id' => 'movieId',
            'number',
            'review',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            userId: $request->user()->id,
            movieId: $request->input('movie_id'),
            number: (int) $request->input('number'),
            review: $request->input('review'),
        );
    }
}
