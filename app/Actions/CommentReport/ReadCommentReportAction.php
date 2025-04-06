<?php

namespace App\Actions\CommentReport;

use App\Models\CommentReport;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadCommentReportAction
{
    /**
     * Повертає запис CommentReport за його ідентифікатором.
     *
     * @param string $id
     * @return CommentReport|null
     * @throws AuthorizationException
     */
    public function execute(string $id): ?CommentReport
    {
        Gate::authorize('view', CommentReport::class);
        return CommentReport::find($id);
    }
}
