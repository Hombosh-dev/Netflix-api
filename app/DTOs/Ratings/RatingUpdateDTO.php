<?php

namespace App\DTOs\Ratings;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class RatingUpdateDTO extends BaseDTO
{
    /**
     * Create a new RatingUpdateDTO instance.
     *
     * @param int|null $number Rating number (1-10)
     * @param string|null $review Review text
     */
    public function __construct(
        public readonly ?int $number = null,
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
            number: $request->has('number') ? (int) $request->input('number') : null,
            review: $request->has('review') ? $request->input('review') : null,
        );
    }
}
