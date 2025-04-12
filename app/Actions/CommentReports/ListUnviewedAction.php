<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;
use Illuminate\Database\Eloquent\Collection;

class ListUnviewedAction
{
    public function execute(): Collection
    {
        return CommentReport::where('is_viewed', false)->with(['user', 'comment'])->get();
    }
}
