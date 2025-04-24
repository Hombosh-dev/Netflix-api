<?php

namespace App\Actions\Persons;

use App\DTOs\Persons\PersonStoreDTO;
use App\Models\Person;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePerson
{
    use AsAction;

    /**
     * Create a new person.
     *
     * @param  PersonStoreDTO  $dto
     * @return Person
     */
    public function handle(PersonStoreDTO $dto): Person
    {
        // Create new person
        $person = new Person();
        $person->name = $dto->name;
        $person->type = $dto->type;
        $person->original_name = $dto->originalName;
        $person->gender = $dto->gender;
        $person->description = $dto->description;
        $person->birthday = $dto->birthday;
        $person->birthplace = $dto->birthplace;
        $person->slug = $dto->slug;
        $person->meta_title = $dto->metaTitle ?? $dto->name;
        $person->meta_description = $dto->metaDescription ?? $dto->description;

        // Handle file uploads
        if ($dto->image) {
            $person->image = $person->handleFileUpload($dto->image, 'people');
        }

        if ($dto->metaImage) {
            $person->meta_image = $person->handleFileUpload($dto->metaImage, 'meta');
        } else if ($dto->image) {
            // Use the main image as meta image if not provided
            $person->meta_image = $person->image;
        }

        $person->save();

        return $person;
    }
}
