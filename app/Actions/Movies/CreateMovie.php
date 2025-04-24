<?php

namespace App\Actions\Movies;

use App\DTOs\Movies\MovieStoreDTO;
use App\Models\Movie;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMovie
{
    use AsAction;

    /**
     * Create a new movie.
     *
     * @param  MovieStoreDTO  $dto
     * @return Movie
     */
    public function handle(MovieStoreDTO $dto): Movie
    {
        // Create new movie
        $movie = new Movie();
        $movie->name = $dto->name;
        $movie->description = $dto->description;
        $movie->kind = $dto->kind;
        $movie->status = $dto->status;
        $movie->studio_id = $dto->studioId;
        $movie->countries = $dto->countries ?? [];
        $movie->aliases = $dto->aliases ?? [];
        $movie->first_air_date = $dto->firstAirDate;
        $movie->last_air_date = $dto->lastAirDate;
        $movie->duration = $dto->duration;
        $movie->imdb_score = $dto->imdbScore;
        $movie->is_published = $dto->isPublished;
        $movie->related = $dto->related ?? [];
        $movie->similars = $dto->similars ?? [];
        $movie->api_sources = $dto->apiSources ?? [];
        $movie->slug = $dto->slug;
        $movie->meta_title = $dto->metaTitle ?? $dto->name;
        $movie->meta_description = $dto->metaDescription ?? $dto->description;

        // Handle file uploads
        if ($dto->poster) {
            $movie->poster = $movie->handleFileUpload($dto->poster, 'posters');
        }

        if ($dto->image_name) {
            $movie->image_name = $movie->handleFileUpload($dto->image_name, 'movies');
        }

        if ($dto->metaImage) {
            $movie->meta_image = $movie->handleFileUpload($dto->metaImage, 'meta');
        }

        // Process attachments if any
        if ($dto->attachments) {
            $movie->attachments = $movie->processAttachments($dto->attachments, 'attachments');
        } else {
            $movie->attachments = [];
        }

        $movie->save();

        // Sync tags if provided
        if ($dto->tagIds) {
            $movie->tags()->sync($dto->tagIds);
        }

        // Sync persons if provided
        if ($dto->personIds) {
            $syncData = [];
            foreach ($dto->personIds as $personId => $pivotData) {
                if (is_array($pivotData)) {
                    $syncData[$personId] = $pivotData;
                } else {
                    $syncData[$pivotData] = ['character_name' => null];
                }
            }
            $movie->persons()->sync($syncData);
        }

        return $movie->load(['studio', 'tags', 'persons']);
    }
}
