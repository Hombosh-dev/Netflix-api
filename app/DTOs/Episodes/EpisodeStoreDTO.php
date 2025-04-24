<?php

namespace App\DTOs\Episodes;

use App\DTOs\BaseDTO;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EpisodeStoreDTO extends BaseDTO
{
    /**
     * Create a new EpisodeStoreDTO instance.
     *
     * @param string $movieId Movie ID
     * @param int $number Episode number
     * @param string $name Episode name
     * @param string $description Episode description
     * @param int|null $duration Duration in minutes
     * @param Carbon|null $airDate Air date
     * @param bool $isFiller Whether the episode is a filler
     * @param array|Collection $pictures Pictures array
     * @param array|Collection $videoPlayers Video players array
     * @param string|null $slug Episode slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly string $movieId,
        public readonly int $number,
        public readonly string $name,
        public readonly string $description,
        public readonly ?int $duration = null,
        public readonly ?Carbon $airDate = null,
        public readonly bool $isFiller = false,
        public readonly array|Collection $pictures = [],
        public readonly array|Collection $videoPlayers = [],
        public readonly ?string $slug = null,
        public readonly ?string $metaTitle = null,
        public readonly ?string $metaDescription = null,
        public readonly ?string $metaImage = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'movie_id' => 'movieId',
            'number',
            'name',
            'description',
            'duration',
            'air_date' => 'airDate',
            'is_filler' => 'isFiller',
            'pictures',
            'video_players' => 'videoPlayers',
            'slug',
            'meta_title' => 'metaTitle',
            'meta_description' => 'metaDescription',
            'meta_image' => 'metaImage',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        // Process arrays
        $pictures = self::processJsonArray($request->input('pictures', []));
        $videoPlayers = self::processVideoPlayers($request->input('video_players', []));

        // Generate slug if not provided
        $slug = $request->input('slug');
        if (!$slug) {
            $slug = Episode::generateSlug($request->input('name'));
        }

        return new static(
            movieId: $request->input('movie_id'),
            number: (int) $request->input('number'),
            name: $request->input('name'),
            description: $request->input('description'),
            duration: $request->has('duration') ? (int) $request->input('duration') : null,
            airDate: $request->has('air_date') ? Carbon::parse($request->input('air_date')) : null,
            isFiller: $request->boolean('is_filler', false),
            pictures: $pictures,
            videoPlayers: $videoPlayers,
            slug: $slug,
            metaTitle: $request->input('meta_title'),
            metaDescription: $request->input('meta_description'),
            metaImage: $request->input('meta_image'),
        );
    }

    /**
     * Process JSON array from request input.
     *
     * @param mixed $input
     * @return array
     */
    private static function processJsonArray($input): array
    {
        if (is_string($input)) {
            return json_decode($input, true) ?? [];
        }

        return is_array($input) ? $input : [];
    }

    /**
     * Process video players array from request input.
     *
     * @param mixed $input
     * @return array
     */
    private static function processVideoPlayers($input): array
    {
        $videoPlayers = self::processJsonArray($input);

        // Ensure all video players have the required structure
        foreach ($videoPlayers as $key => $player) {
            // Convert name to enum value if it's a string
            if (isset($player['name']) && is_string($player['name'])) {
                try {
                    $videoPlayers[$key]['name'] = VideoPlayerName::from($player['name'])->value;
                } catch (\ValueError $e) {
                    // Invalid name, use default
                    $videoPlayers[$key]['name'] = VideoPlayerName::KODIK->value;
                }
            }

            // Convert quality to enum value if it's a string
            if (isset($player['quality']) && is_string($player['quality'])) {
                try {
                    $videoPlayers[$key]['quality'] = VideoQuality::from($player['quality'])->value;
                } catch (\ValueError $e) {
                    // Invalid quality, use default
                    $videoPlayers[$key]['quality'] = VideoQuality::HD->value;
                }
            }

            // Ensure all required fields are present
            $videoPlayers[$key]['file_url'] = $player['file_url'] ?? '';
            $videoPlayers[$key]['dubbing'] = $player['dubbing'] ?? '';
            $videoPlayers[$key]['locale_code'] = $player['locale_code'] ?? 'uk';
        }

        return $videoPlayers;
    }
}
