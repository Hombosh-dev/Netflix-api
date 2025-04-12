<?php

namespace App\Actions\CommentReports;

use App\Enums\CommentReportType;
use App\Models\CommentReport;
use Illuminate\Database\Eloquent\Collection;

class ListByTypeAction
{
    public function execute(CommentReportType $type): Collection
    {
        return CommentReport::where('type', $type->value)->with(['user', 'comment'])->get();
    }
}
