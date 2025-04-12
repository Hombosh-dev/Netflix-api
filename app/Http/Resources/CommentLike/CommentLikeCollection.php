<?php

namespace App\Http\Resources\CommentLike;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentLikeCollection extends ResourceCollection
{
    public $collects = CommentLikeResource::class;

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
