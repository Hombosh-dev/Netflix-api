<?php

namespace App\Actions\Episodes;

use App\DTOs\Episodes\EpisodeUpdateDTO;
use App\Models\Episode;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEpisode
{
    use AsAction;

    /**
     * Update an existing episode.
     *
     * @param  Episode  $episode
     * @param  EpisodeUpdateDTO  $dto
     * @return Episode
     */
    public function handle(Episode $episode, EpisodeUpdateDTO $dto): Episode
    {
        // Update the episode
        if ($dto->movieId !== null) {
            $episode->movie_id = $dto->movieId;
        }

        if ($dto->number !== null) {
            $episode->number = $dto->number;
        }

        if ($dto->name !== null) {
            $episode->name = $dto->name;
        }

        if ($dto->description !== null) {
            $episode->description = $dto->description;
        }

        if ($dto->duration !== null) {
            $episode->duration = $dto->duration;
        }

        if ($dto->airDate !== null) {
            $episode->air_date = $dto->airDate;
        }

        if ($dto->isFiller !== null) {
            $episode->is_filler = $dto->isFiller;
        }

        if ($dto->pictures !== null) {
            $episode->pictures = $episode->processFilesArray($dto->pictures, 'episodes', $episode->pictures->toArray());
        }

        if ($dto->videoPlayers !== null) {
            $episode->video_players = $dto->videoPlayers;
        }

        if ($dto->slug !== null) {
            $episode->slug = $dto->slug;
        }

        if ($dto->metaTitle !== null) {
            $episode->meta_title = $dto->metaTitle;
        }

        if ($dto->metaDescription !== null) {
            $episode->meta_description = $dto->metaDescription;
        }

        if ($dto->metaImage !== null) {
            $episode->meta_image = $episode->handleFileUpload($dto->metaImage, 'meta', $episode->meta_image);
        }

        $episode->save();

        return $episode->load(['movie']);
    }
}
