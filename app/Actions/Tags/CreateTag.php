<?php

namespace App\Actions\Tags;

use App\DTOs\Tags\TagStoreDTO;
use App\Models\Tag;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTag
{
    use AsAction;

    /**
     * Create a new tag.
     *
     * @param  TagStoreDTO  $dto
     * @return Tag
     */
    public function handle(TagStoreDTO $dto): Tag
    {
        // Create new tag
        $tag = new Tag();
        $tag->name = $dto->name;
        $tag->description = $dto->description;
        $tag->is_genre = $dto->isGenre;
        $tag->aliases = $dto->aliases;
        $tag->slug = $dto->slug;
        $tag->meta_title = $dto->metaTitle ?? $dto->name;
        $tag->meta_description = $dto->metaDescription ?? $dto->description;

        // Handle file uploads
        if ($dto->image) {
            $tag->image = $tag->handleFileUpload($dto->image, 'tags');
        }

        if ($dto->metaImage) {
            $tag->meta_image = $tag->handleFileUpload($dto->metaImage, 'meta');
        } else if ($dto->image) {
            // Use the main image as meta image if not provided
            $tag->meta_image = $tag->image;
        }

        $tag->save();

        return $tag;
    }
}
