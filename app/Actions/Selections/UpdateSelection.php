<?php

namespace App\Actions\Selections;

use App\DTOs\Selections\SelectionUpdateDTO;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSelection
{
    use AsAction;

    /**
     * Update an existing selection.
     *
     * @param  Selection  $selection
     * @param  SelectionUpdateDTO  $dto
     * @return Selection
     */
    public function handle(Selection $selection, SelectionUpdateDTO $dto): Selection
    {
        // Update the selection
        if ($dto->name !== null) {
            $selection->name = $dto->name;
        }

        if ($dto->description !== null) {
            $selection->description = $dto->description;
        }

        if ($dto->userId !== null) {
            $selection->user_id = $dto->userId;
        }

        if ($dto->isPublished !== null) {
            $selection->is_published = $dto->isPublished;
        }

        if ($dto->slug !== null) {
            $selection->slug = $dto->slug;
        }

        if ($dto->metaTitle !== null) {
            $selection->meta_title = $dto->metaTitle;
        }

        if ($dto->metaDescription !== null) {
            $selection->meta_description = $dto->metaDescription;
        }

        if ($dto->metaImage !== null) {
            $selection->meta_image = $selection->handleFileUpload($dto->metaImage, 'selections', $selection->meta_image);
        }

        $selection->save();

        // Sync movies if provided
        if ($dto->movieIds !== null) {
            $movies = Movie::whereIn('id', $dto->movieIds)->get();
            $selection->movies()->sync($movies);
        }

        // Sync persons if provided
        if ($dto->personIds !== null) {
            $persons = Person::whereIn('id', $dto->personIds)->get();
            $selection->persons()->sync($persons);
        }

        return $selection->load(['user', 'movies', 'persons'])->loadCount(['movies', 'persons']);
    }
}
