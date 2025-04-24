<?php

namespace App\Actions\Search;

use App\DTOs\Search\AutocompleteDTO;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Tag;
use Lorisleiva\Actions\Concerns\AsAction;

class PerformAutocomplete
{
    use AsAction;

    /**
     * Execute the autocomplete search.
     *
     * @param AutocompleteDTO $dto
     * @return array
     */
    public function handle(AutocompleteDTO $dto): array
    {
        // Якщо запит порожній або занадто короткий, повертаємо порожній масив
        if (empty($dto->query) || strlen($dto->query) < 2) {
            return [];
        }

        // Використовуємо колекцію для збору результатів
        return collect()
            ->when(true, function ($collection) use ($dto) {
                return $collection->merge($this->getMovies($dto->query));
            })
            ->when(true, function ($collection) use ($dto) {
                return $collection->merge($this->getPeople($dto->query));
            })
            ->when(true, function ($collection) use ($dto) {
                return $collection->merge($this->getTags($dto->query));
            })
            ->toArray();
    }

    /**
     * Get movie autocomplete results.
     *
     * @param string $query
     * @return array
     */
    private function getMovies(string $query): array
    {
        return Movie::search($query)
            ->take(5)
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'text' => $movie->name,
                    'type' => 'movie',
                    'image' => $movie->image_name,
                    'url' => "/movies/{$movie->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Get person autocomplete results.
     *
     * @param string $query
     * @return array
     */
    private function getPeople(string $query): array
    {
        return Person::search($query)
            ->take(5)
            ->get()
            ->map(function ($person) {
                return [
                    'id' => $person->id,
                    'text' => $person->name,
                    'type' => 'person',
                    'image' => $person->image,
                    'url' => "/people/{$person->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Get tag autocomplete results.
     *
     * @param string $query
     * @return array
     */
    private function getTags(string $query): array
    {
        return Tag::search($query)
            ->take(3)
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'text' => $tag->name,
                    'type' => 'tag',
                    'image' => $tag->image,
                    'url' => "/tags/{$tag->slug}",
                ];
            })
            ->toArray();
    }
}
