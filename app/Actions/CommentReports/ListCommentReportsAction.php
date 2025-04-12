<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;
use Illuminate\Database\Eloquent\Collection;

class ListCommentReportsAction
{
    public function execute(): Collection
    {
        return CommentReport::with(['user', 'comment'])->get();
    }
}
