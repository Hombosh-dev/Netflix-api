<?php

namespace App\DTOs\CommentLikes;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentLikeStoreDTO extends BaseDTO
{
    /**
     * Create a new CommentLikeStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $commentId Comment ID
     * @param bool $isLiked Whether it's a like (true) or dislike (false)
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $commentId,
        public readonly bool $isLiked = true,
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
            'comment_id' => 'commentId',
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
            userId: $request->user()->id,
            commentId: $request->input('comment_id'),
            isLiked: $request->boolean('is_liked', true),
        );
    }
}
