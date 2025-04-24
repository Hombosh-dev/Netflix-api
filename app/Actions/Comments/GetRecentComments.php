<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\CommentRecentDTO;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetRecentComments
{
    use AsAction;

    /**
     * Get recent comments.
     *
     * @param  CommentRecentDTO  $dto
     * @return Collection
     */
    public function handle(CommentRecentDTO $dto): Collection
    {
        return Comment::with(['user'])
            ->withCount('likes')
            ->orderByDesc('created_at')
            ->limit($dto->limit)
            ->get();
    }
}
