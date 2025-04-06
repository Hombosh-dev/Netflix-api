<?php

namespace App\Actions\CommentReport;

use App\Models\CommentReport;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class DeleteCommentReportAction
{
    /**
     * Видаляє запис CommentReport.
     *
     * @param CommentReport $commentReport
     * @return bool|null
     * @throws AuthorizationException
     */
    public function execute(CommentReport $commentReport): ?bool
    {
        Gate::authorize('delete', $commentReport);
        return $commentReport->delete();
    }
}
