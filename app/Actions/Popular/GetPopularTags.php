<?php

namespace App\Actions\Popular;

use App\DTOs\Popular\PopularTagsDTO;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPopularTags
{
    use AsAction;

    /**
     * Get popular tags.
     *
     * @param PopularTagsDTO $dto
     * @return Collection
     */
    public function handle(PopularTagsDTO $dto): Collection
    {
        return Tag::withCount('movies')
            ->orderBy('movies_count', 'desc')
            ->take($dto->limit)
            ->get();
    }
}
