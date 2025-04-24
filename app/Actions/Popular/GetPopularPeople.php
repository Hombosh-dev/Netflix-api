<?php

namespace App\Actions\Popular;

use App\DTOs\Popular\PopularPeopleDTO;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPopularPeople
{
    use AsAction;

    /**
     * Get popular people.
     *
     * @param PopularPeopleDTO $dto
     * @return Collection
     */
    public function handle(PopularPeopleDTO $dto): Collection
    {
        return Person::withCount('movies')
            ->orderBy('movies_count', 'desc')
            ->take($dto->limit)
            ->get();
    }
}
