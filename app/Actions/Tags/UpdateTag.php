<?php

namespace App\Actions\Tags;

use App\DTOs\Tags\TagUpdateDTO;
use App\Models\Tag;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTag
{
    use AsAction;

    /**
     * Update an existing tag.
     *
     * @param  Tag  $tag
     * @param  TagUpdateDTO  $dto
     * @return Tag
     */
    public function handle(Tag $tag, TagUpdateDTO $dto): Tag
    {
        // Update the tag
        if ($dto->name !== null) {
            $tag->name = $dto->name;
        }

        if ($dto->description !== null) {
            $tag->description = $dto->description;
        }

        if ($dto->isGenre !== null) {
            $tag->is_genre = $dto->isGenre;
        }

        if ($dto->image !== null) {
            $tag->image = $tag->handleFileUpload($dto->image, 'tags', $tag->image);
        }

        if ($dto->aliases !== null) {
            $tag->aliases = $dto->aliases;
        }

        if ($dto->slug !== null) {
            $tag->slug = $dto->slug;
        }

        if ($dto->metaTitle !== null) {
            $tag->meta_title = $dto->metaTitle;
        }

        if ($dto->metaDescription !== null) {
            $tag->meta_description = $dto->metaDescription;
        }

        if ($dto->metaImage !== null) {
            $tag->meta_image = $tag->handleFileUpload($dto->metaImage, 'meta', $tag->meta_image);
        }

        $tag->save();

        return $tag->loadCount('movies');
    }
}
