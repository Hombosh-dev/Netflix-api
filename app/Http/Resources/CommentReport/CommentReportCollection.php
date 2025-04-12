<?php

namespace App\Http\Resources\CommentReport;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CommentReportCollection extends ResourceCollection
{
    public $collects = CommentReportResource::class;

    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return parent::toArray($request);
    }
}
