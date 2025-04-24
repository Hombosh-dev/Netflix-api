<?php

namespace App\DTOs\Comments;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentRecentDTO extends BaseDTO
{
    /**
     * Create a new CommentRecentDTO instance.
     *
     * @param int $limit Number of comments to return
     */
    public function __construct(
        public readonly int $limit = 10,
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
            'limit',
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
            limit: (int) $request->input('limit', 10),
        );
    }
}
