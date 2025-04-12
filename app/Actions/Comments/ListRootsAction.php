<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class ListRootsAction
{
    public function execute(): Collection
    {
        return Comment::whereNull('parent_id')->get();
    }
}
