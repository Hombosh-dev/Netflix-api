<?php

namespace App\DTOs\Comments;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentIndexDTO extends BaseDTO
{
    /**
     * Create a new CommentIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     * @param bool|null $isSpoiler Filter by spoiler status
     * @param string|null $userId Filter by user ID
     * @param string|null $commentableType Filter by commentable type
     * @param string|null $commentableId Filter by commentable ID
     * @param bool|null $isRoot Filter by root status (not replies)
     * @param string|null $parentId Filter by parent comment ID
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
        public readonly ?bool $isSpoiler = null,
        public readonly ?string $userId = null,
        public readonly ?string $commentableType = null,
        public readonly ?string $commentableId = null,
        public readonly ?bool $isRoot = null,
        public readonly ?string $parentId = null,
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
            'q' => 'query',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
            'is_spoiler' => 'isSpoiler',
            'user_id' => 'userId',
            'commentable_type' => 'commentableType',
            'commentable_id' => 'commentableId',
            'is_root' => 'isRoot',
            'parent_id' => 'parentId',
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
            query: $request->input('q'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            isSpoiler: $request->has('is_spoiler') ? (bool) $request->input('is_spoiler') : null,
            userId: $request->input('user_id'),
            commentableType: $request->input('commentable_type'),
            commentableId: $request->input('commentable_id'),
            isRoot: $request->has('is_root') ? (bool) $request->input('is_root') : null,
            parentId: $request->input('parent_id'),
        );
    }
}
