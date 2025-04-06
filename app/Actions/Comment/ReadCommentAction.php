<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadCommentAction
{
    /**
     * Повертає запис Comment за його ідентифікатором.
     *
     * @param string $id
     * @return Comment|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?Comment
    {
        Gate::authorize('view', Comment::class);
        return Comment::find($id);
    }
}
