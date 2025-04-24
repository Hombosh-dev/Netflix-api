<?php

namespace App\DTOs\CommentReports;

use App\DTOs\BaseDTO;
use App\Enums\CommentReportType;
use Illuminate\Http\Request;

class CommentReportIndexDTO extends BaseDTO
{
    /**
     * Create a new CommentReportIndexDTO instance.
     *
     * @param string|null $commentId Filter by comment ID
     * @param string|null $userId Filter by user ID
     * @param CommentReportType|null $type Filter by report type
     * @param bool|null $isViewed Filter by viewed status
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     */
    public function __construct(
        public readonly ?string $commentId = null,
        public readonly ?string $userId = null,
        public readonly ?CommentReportType $type = null,
        public readonly ?bool $isViewed = null,
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
            'type',
            'is_viewed' => 'isViewed',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
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
        $type = null;
        if ($request->has('type') && $request->input('type')) {
            try {
                $type = CommentReportType::from($request->input('type'));
            } catch (\ValueError $e) {
                // Invalid type, ignore
            }
        }

        return new static(
            commentId: $request->input('comment_id'),
            userId: $request->input('user_id'),
            type: $type,
            isViewed: $request->has('is_viewed') ? $request->boolean('is_viewed') : null,
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
        );
    }
}
