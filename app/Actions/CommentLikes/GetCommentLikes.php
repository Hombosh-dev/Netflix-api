<?php

namespace App\Actions\CommentLikes;

use App\DTOs\CommentLikes\CommentLikeIndexDTO;
use App\Models\CommentLike;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCommentLikes
{
    use AsAction;

    /**
     * Get paginated list of comment likes with filtering, searching, and sorting.
     *
     * @param  CommentLikeIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(CommentLikeIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = CommentLike::query()->with(['user', 'comment']);

        // Apply filters
        if ($dto->commentId) {
            $query->where('comment_id', $dto->commentId);
        }

        if ($dto->userId) {
            $query->where('user_id', $dto->userId);
        }

        if ($dto->isLiked !== null) {
            $query->where('is_liked', $dto->isLiked);
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
