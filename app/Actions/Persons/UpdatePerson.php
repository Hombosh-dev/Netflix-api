<?php

namespace App\Actions\Persons;

use App\DTOs\Persons\PersonUpdateDTO;
use App\Models\Person;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePerson
{
    use AsAction;

    /**
     * Update an existing person.
     *
     * @param  Person  $person
     * @param  PersonUpdateDTO  $dto
     * @return Person
     */
    public function handle(Person $person, PersonUpdateDTO $dto): Person
    {
        // Update the person
        if ($dto->name !== null) {
            $person->name = $dto->name;
        }

        if ($dto->type !== null) {
            $person->type = $dto->type;
        }

        if ($dto->originalName !== null) {
            $person->original_name = $dto->originalName;
        }

        if ($dto->gender !== null) {
            $person->gender = $dto->gender;
        }

        if ($dto->image !== null) {
            $person->image = $person->handleFileUpload($dto->image, 'people', $person->image);
        }

        if ($dto->description !== null) {
            $person->description = $dto->description;
        }

        if ($dto->birthday !== null) {
            $person->birthday = $dto->birthday;
        }

        if ($dto->birthplace !== null) {
            $person->birthplace = $dto->birthplace;
        }

        if ($dto->slug !== null) {
            $person->slug = $dto->slug;
        }

        if ($dto->metaTitle !== null) {
            $person->meta_title = $dto->metaTitle;
        }

        if ($dto->metaDescription !== null) {
            $person->meta_description = $dto->metaDescription;
        }

        if ($dto->metaImage !== null) {
            $person->meta_image = $person->handleFileUpload($dto->metaImage, 'meta', $person->meta_image);
        }

        $person->save();

        return $person->load('movies');
    }
}
