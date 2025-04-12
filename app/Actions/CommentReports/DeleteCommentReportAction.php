<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;
use Illuminate\Support\Facades\DB;

class DeleteCommentReportAction
{
    public function execute(CommentReport $commentReport): void
    {
        DB::transaction(function () use ($commentReport) {
            $commentReport->delete();
        });
    }
}
