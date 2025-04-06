<?php

namespace App\Actions\CommentReport;

use App\Models\CommentReport;
use Illuminate\Support\Facades\Gate;

class UpdateCommentReportAction
{
    /**
     * Оновлює існуючий запис CommentReport.
     *
     * @param CommentReport $commentReport
     * @param array{
     *     comment_id?: string,
     *     user_id?: string,
     *     type?: string,
     *     is_viewed?: bool
     * } $data
     * @return bool
     */
    public function execute(CommentReport $commentReport, array $data): bool
    {
        Gate::forUser($commentReport->user)->authorize('update', $commentReport);
        return $commentReport->update($data);
    }
}
