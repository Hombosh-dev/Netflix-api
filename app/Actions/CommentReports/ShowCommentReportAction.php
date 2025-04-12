<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;

class ShowCommentReportAction
{
    public function execute(CommentReport $commentReport): CommentReport
    {
        return $commentReport->load(['user', 'comment']);
    }
}
