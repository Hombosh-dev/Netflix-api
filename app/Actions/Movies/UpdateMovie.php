<?php

namespace App\Actions\Movies;

use App\DTOs\Movies\MovieUpdateDTO;
use App\Models\Movie;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMovie
{
    use AsAction;

    /**
     * Update an existing movie.
     *
     * @param  Movie  $movie
     * @param  MovieUpdateDTO  $dto
     * @return Movie
     */
    public function handle(Movie $movie, MovieUpdateDTO $dto): Movie
    {
        // Update the movie
        if ($dto->name !== null) {
            $movie->name = $dto->name;
        }

        if ($dto->description !== null) {
            $movie->description = $dto->description;
        }

        if ($dto->kind !== null) {
            $movie->kind = $dto->kind;
        }

        if ($dto->status !== null) {
            $movie->status = $dto->status;
        }

        if ($dto->studioId !== null) {
            $movie->studio_id = $dto->studioId;
        }

        if ($dto->poster !== null) {
            $movie->poster = $movie->handleFileUpload($dto->poster, 'posters', $movie->poster);
        }

        if ($dto->backdrop !== null) {
            $movie->backdrop = $movie->handleFileUpload($dto->backdrop, 'backdrops', $movie->backdrop);
        }

        if ($dto->image_name !== null) {
            $movie->image_name = $movie->handleFileUpload($dto->image_name, 'movies', $movie->image_name);
        }

        if ($dto->countries !== null) {
            $movie->countries = $dto->countries;
        }

        if ($dto->aliases !== null) {
            $movie->aliases = $dto->aliases;
        }

        if ($dto->firstAirDate !== null) {
            $movie->first_air_date = $dto->firstAirDate;
        }

        if ($dto->lastAirDate !== null) {
            $movie->last_air_date = $dto->lastAirDate;
        }

        if ($dto->duration !== null) {
            $movie->duration = $dto->duration;
        }

        if ($dto->imdbScore !== null) {
            $movie->imdb_score = $dto->imdbScore;
        }

        if ($dto->isPublished !== null) {
            $movie->is_published = $dto->isPublished;
        }

        if ($dto->attachments !== null) {
            $movie->attachments = $movie->processAttachments($dto->attachments, 'attachments');
        }

        if ($dto->related !== null) {
            $movie->related = $dto->related;
        }

        if ($dto->similars !== null) {
            $movie->similars = $dto->similars;
        }

        if ($dto->apiSources !== null) {
            $movie->api_sources = $dto->apiSources;
        }

        if ($dto->slug !== null) {
            $movie->slug = $dto->slug;
        }

        if ($dto->metaTitle !== null) {
            $movie->meta_title = $dto->metaTitle;
        }

        if ($dto->metaDescription !== null) {
            $movie->meta_description = $dto->metaDescription;
        }

        if ($dto->metaImage !== null) {
            $movie->meta_image = $movie->handleFileUpload($dto->metaImage, 'meta', $movie->meta_image);
        }

        $movie->save();

        // Sync tags if provided
        if ($dto->tagIds !== null) {
            $movie->tags()->sync($dto->tagIds);
        }

        // Sync persons if provided
        if ($dto->personIds !== null) {
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
