<?php

namespace App\DTOs\CommentLikes;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentLikeUpdateDTO extends BaseDTO
{
    /**
     * Create a new CommentLikeUpdateDTO instance.
     *
     * @param bool|null $isLiked Whether it's a like (true) or dislike (false)
     */
    public function __construct(
        public readonly ?bool $isLiked = null,
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
            'is_liked' => 'isLiked',
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
            isLiked: $request->has('is_liked') ? $request->boolean('is_liked') : null,
        );
    }
}
