<?php

namespace App\Actions\Selections;

use App\DTOs\Selections\SelectionStoreDTO;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSelection
{
    use AsAction;

    /**
     * Create a new selection.
     *
     * @param  SelectionStoreDTO  $dto
     * @return Selection
     */
    public function handle(SelectionStoreDTO $dto): Selection
    {
        // Create new selection
        $selection = new Selection();
        $selection->name = $dto->name;
        $selection->description = $dto->description;
        $selection->user_id = $dto->userId;
        $selection->is_published = $dto->isPublished;
        $selection->slug = $dto->slug;
        $selection->meta_title = $dto->metaTitle ?? $dto->name;
        $selection->meta_description = $dto->metaDescription ?? $dto->description;

        // Handle file uploads
        if ($dto->metaImage) {
            $selection->meta_image = $selection->handleFileUpload($dto->metaImage, 'selections');
        }

        $selection->save();

        // Attach movies if provided
        if (!empty($dto->movieIds)) {
            $movies = Movie::whereIn('id', $dto->movieIds)->get();
            $selection->movies()->attach($movies);
        }

        // Attach persons if provided
        if (!empty($dto->personIds)) {
            $persons = Person::whereIn('id', $dto->personIds)->get();
            $selection->persons()->attach($persons);
        }

        return $selection->load(['user', 'movies', 'persons'])->loadCount(['movies', 'persons']);
    }
}
