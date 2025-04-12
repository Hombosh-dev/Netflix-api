<?php

namespace App\Actions\CommentReports;

use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListByUserAction
{
    public function execute(User $user): Collection
    {
        return CommentReport::where('user_id', $user->id)->with(['comment'])->get();
    }
}
