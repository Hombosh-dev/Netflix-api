<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\CommentIndexDTO;
use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetComments
{
    use AsAction;

    /**
     * Get paginated list of comments with filtering, searching, and sorting.
     *
     * @param  CommentIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(CommentIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Comment::query()->with(['user']);

        // Apply search if query is provided
        if ($dto->query) {
            $query->where('body', 'like', "%{$dto->query}%");
        }

        // Apply filters
        if ($dto->isSpoiler !== null) {
            $query->where('is_spoiler', $dto->isSpoiler);
        }

        if ($dto->userId) {
            $query->forUser($dto->userId);
        }

        if ($dto->commentableType && $dto->commentableId) {
            $query->forCommentable($dto->commentableType, $dto->commentableId);
        } elseif ($dto->commentableType) {
            $query->where('commentable_type', $dto->commentableType);
        }

        if ($dto->isRoot !== null) {
            if ($dto->isRoot) {
                $query->roots();
            } else {
                $query->replies();
            }
        }

        if ($dto->parentId) {
            $query->where('parent_id', $dto->parentId);
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

        if ($sortField === 'likes_count') {
            $query->withCount('likes')->orderBy('likes_count', $direction);
        } else {
            $query->orderBy($sortField, $direction);
        }

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
