<?php

namespace App\Actions\Popular;

use App\DTOs\Popular\PopularSelectionsDTO;
use App\Models\Selection;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPopularSelections
{
    use AsAction;

    /**
     * Get popular selections.
     *
     * @param PopularSelectionsDTO $dto
     * @return Collection
     */
    public function handle(PopularSelectionsDTO $dto): Collection
    {
        return Selection::withCount(['movies', 'userLists'])
            ->where('is_published', true)
            ->orderBy('user_lists_count', 'desc')
            ->take($dto->limit)
            ->get();
    }
}
