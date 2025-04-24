<?php

namespace App\Actions\Search;

use App\DTOs\Search\SearchDTO;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tag;
use Lorisleiva\Actions\Concerns\AsAction;

class PerformSearch
{
    use AsAction;

    /**
     * Execute the search across multiple content types.
     *
     * @param  SearchDTO  $dto
     * @return array
     */
    public function handle(SearchDTO $dto): array
    {
        // Якщо запит порожній, повертаємо порожній масив
        if (empty($dto->query)) {
            return [];
        }

        return collect()
            ->when(in_array('movies', $dto->types), function ($collection) use ($dto) {
                return $collection->put('movies', $this->searchMovies($dto->query));
            })
            ->when(in_array('people', $dto->types), function ($collection) use ($dto) {
                return $collection->put('people', $this->searchPeople($dto->query));
            })
            ->when(in_array('studios', $dto->types), function ($collection) use ($dto) {
                return $collection->put('studios', $this->searchStudios($dto->query));
            })
            ->when(in_array('tags', $dto->types), function ($collection) use ($dto) {
                return $collection->put('tags', $this->searchTags($dto->query));
            })
            ->when(in_array('selections', $dto->types), function ($collection) use ($dto) {
                return $collection->put('selections', $this->searchSelections($dto->query));
            })
            ->toArray();
    }

    /**
     * Search for movies.
     *
     * @param  string  $query
     * @return array
     */
    private function searchMovies(string $query): array
    {
        return Movie::search($query)
            ->take(10)
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'name' => $movie->name,
                    'slug' => $movie->slug,
                    'image' => $movie->poster_url,
                    'kind' => $movie->kind->value,
                    'year' => $movie->release_year,
                ];
            })
            ->toArray();
    }

    /**
     * Search for people.
     *
     * @param  string  $query
     * @return array
     */
    private function searchPeople(string $query): array
    {
        return Person::search($query)
            ->take(10)
            ->get()
            ->map(function ($person) {
                return [
                    'id' => $person->id,
                    'name' => $person->name,
                    'slug' => $person->slug,
                    'image' => $person->image,
                    'type' => $person->type->value,
                ];
            })
            ->toArray();
    }

    /**
     * Search for studios.
     *
     * @param  string  $query
     * @return array
     */
    private function searchStudios(string $query): array
    {
        return Studio::search($query)
            ->take(10)
            ->get()
            ->map(function ($studio) {
                return [
                    'id' => $studio->id,
                    'name' => $studio->name,
                    'slug' => $studio->slug,
                    'image' => $studio->image,
                ];
            })
            ->toArray();
    }

    /**
     * Search for tags.
     *
     * @param  string  $query
     * @return array
     */
    private function searchTags(string $query): array
    {
        return Tag::search($query)
            ->take(10)
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'image' => $tag->image,
                ];
            })
            ->toArray();
    }

    /**
     * Search for selections.
     *
     * @param  string  $query
     * @return array
     */
    private function searchSelections(string $query): array
    {
        return Selection::search($query)
            ->take(10)
            ->get()
            ->map(function ($selection) {
                return [
                    'id' => $selection->id,
                    'name' => $selection->name,
                    'slug' => $selection->slug,
                    'image' => $selection->meta_image,
                ];
            })
            ->toArray();
    }
}
