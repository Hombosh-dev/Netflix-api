<?php

namespace App\Actions\CommentLike;

use App\Models\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadCommentLikeAction
{
    use AsAction;

    /**
     * Повертає запис CommentLike за його ідентифікатором.
     *
     * @param string $id
     * @return CommentLike|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?CommentLike
    {
        Gate::authorize('view', CommentLike::class);
        return CommentLike::find($id);
    }
}
