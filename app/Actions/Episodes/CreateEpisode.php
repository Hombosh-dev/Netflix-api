<?php

namespace App\Actions\Episodes;

use App\DTOs\Episodes\EpisodeStoreDTO;
use App\Models\Episode;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateEpisode
{
    use AsAction;

    /**
     * Create a new episode.
     *
     * @param  EpisodeStoreDTO  $dto
     * @return Episode
     */
    public function handle(EpisodeStoreDTO $dto): Episode
    {
        // Create new episode
        $episode = new Episode();
        $episode->movie_id = $dto->movieId;
        $episode->number = $dto->number;
        $episode->name = $dto->name;
        $episode->description = $dto->description;
        $episode->duration = $dto->duration;
        $episode->air_date = $dto->airDate;
        $episode->is_filler = $dto->isFiller;
        $episode->video_players = $dto->videoPlayers;
        $episode->slug = $dto->slug;
        $episode->meta_title = $dto->metaTitle ?? $dto->name;
        $episode->meta_description = $dto->metaDescription ?? $dto->description;

        // Handle file uploads
        if ($dto->pictures) {
            $episode->pictures = $episode->processFilesArray($dto->pictures, 'episodes');
        } else {
            $episode->pictures = [];
        }

        if ($dto->metaImage) {
            $episode->meta_image = $episode->handleFileUpload($dto->metaImage, 'meta');
        } else if (!empty($episode->pictures)) {
            // Use the first picture as meta image if not provided
            $episode->meta_image = is_array($episode->pictures) ? reset($episode->pictures) : $episode->pictures;
        }

        $episode->save();

        return $episode->load(['movie']);
    }
}
