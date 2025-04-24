<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCommentLikes
{
    use AsAction;

    /**
     * Get likes for a specific comment.
     *
     * @param  Comment  $comment
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function handle(Comment $comment, int $perPage = 15): LengthAwarePaginator
    {
        return $comment->likes()->with('user')->paginate($perPage);
    }
}
