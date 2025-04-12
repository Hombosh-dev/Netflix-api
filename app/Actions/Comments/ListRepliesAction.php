<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class ListRepliesAction
{
    public function execute(): Collection
    {
        return Comment::whereNotNull('parent_id')->get();
    }
}
