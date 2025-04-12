<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    public $collects = CommentResource::class;

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
