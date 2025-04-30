<?php

namespace App\DTOs\CommentLikes;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentLikeIndexDTO extends BaseDTO
{
    /**
     * Create a new CommentLikeIndexDTO instance.
     *
     * @param string|null $commentId Filter by comment ID
     * @param string|null $userId Filter by user ID
     * @param bool|null $isLiked Filter by like/dislike status
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     */
    public function __construct(
        public readonly ?string $commentId = null,
        public readonly ?string $userId = null,
        public readonly ?bool $isLiked = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
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
            'comment_id' => 'commentId',
            'user_id' => 'userId',
            'is_liked' => 'isLiked',
            'page' => 'page',
            'per_page' => 'perPage',
            'sort' => 'sort',
            'direction' => 'direction',
        ];
    }
}
