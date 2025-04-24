<?php

namespace App\Actions\CommentReports;

use App\DTOs\CommentReports\CommentReportIndexDTO;
use App\Models\CommentReport;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCommentReports
{
    use AsAction;

    /**
     * Get paginated list of comment reports with filtering, searching, and sorting.
     *
     * @param  CommentReportIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(CommentReportIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = CommentReport::query()->with(['user', 'comment']);

        // Apply filters
        if ($dto->commentId) {
            $query->where('comment_id', $dto->commentId);
        }

        if ($dto->userId) {
            $query->where('user_id', $dto->userId);
        }

        if ($dto->type !== null) {
            $query->where('type', $dto->type);
        }

        if ($dto->isViewed !== null) {
            $query->where('is_viewed', $dto->isViewed);
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';
        $query->orderBy($sortField, $direction);

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
