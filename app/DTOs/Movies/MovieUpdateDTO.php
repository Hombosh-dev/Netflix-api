<?php

namespace App\DTOs\Movies;

use App\DTOs\BaseDTO;
use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\Status;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MovieUpdateDTO extends BaseDTO
{
    /**
     * Create a new MovieUpdateDTO instance.
     *
     * @param string|null $name Movie name
     * @param string|null $description Movie description
     * @param Kind|null $kind Movie kind
     * @param Status|null $status Movie status
     * @param string|null $studioId Studio ID
     * @param string|null $poster Poster image path
     * @param string|null $backdrop Backdrop image path
     * @param string|null $image_name Main image path
     * @param array|Collection|null $countries Countries array
     * @param array|Collection|null $aliases Aliases array
     * @param string|null $firstAirDate First air date
     * @param string|null $lastAirDate Last air date
     * @param int|null $duration Duration in minutes
     * @param float|null $imdbScore IMDb score
     * @param bool|null $isPublished Whether the movie is published
     * @param array|Collection|null $attachments Attachments array
     * @param array|Collection|null $related Related movies array
     * @param array|Collection|null $similars Similar movies array
     * @param array|Collection|null $apiSources API sources array
     * @param array|null $tagIds Tag IDs array
     * @param array|null $personIds Person IDs array with pivot data
     * @param string|null $slug Movie slug
     * @param string|null $metaTitle SEO meta title
     * @param string|null $metaDescription SEO meta description
     * @param string|null $metaImage SEO meta image
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?Kind $kind = null,
        public readonly ?Status $status = null,
        public readonly ?string $studioId = null,
        public readonly ?string $poster = null,
        public readonly ?string $backdrop = null,
        public readonly ?string $image_name = null,
        public readonly array|Collection|null $countries = null,
        public readonly array|Collection|null $aliases = null,
        public readonly ?string $firstAirDate = null,
        public readonly ?string $lastAirDate = null,
        public readonly ?int $duration = null,
        public readonly ?float $imdbScore = null,
        public readonly ?bool $isPublished = null,
        public readonly array|Collection|null $attachments = null,
        public readonly array|Collection|null $related = null,
        public readonly array|Collection|null $similars = null,
        public readonly array|Collection|null $apiSources = null,
        public readonly ?array $tagIds = null,
        public readonly ?array $personIds = null,
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
            'name',
            'description',
            'kind',
            'status',
            'studio_id' => 'studioId',
            'poster',
            'backdrop',
            'image_name',
            'countries',
            'aliases',
            'first_air_date' => 'firstAirDate',
            'last_air_date' => 'lastAirDate',
            'duration',
            'imdb_score' => 'imdbScore',
            'is_published' => 'isPublished',
            'attachments',
            'related',
            'similars',
            'api_sources' => 'apiSources',
            'tag_ids' => 'tagIds',
            'person_ids' => 'personIds',
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
        // Process kind and status
        $kind = $request->has('kind') ? Kind::from($request->input('kind')) : null;
        $status = $request->has('status') ? Status::from($request->input('status')) : null;

        // Process arrays
        $countries = $request->has('countries') ? self::processJsonArray($request->input('countries')) : null;
        $aliases = $request->has('aliases') ? self::processJsonArray($request->input('aliases')) : null;
        $attachments = $request->has('attachments') ? self::processAttachments($request->input('attachments')) : null;
        $related = $request->has('related') ? self::processRelatedMovies($request->input('related')) : null;
        $similars = $request->has('similars') ? self::processJsonArray($request->input('similars')) : null;
        $apiSources = $request->has('api_sources') ? self::processApiSources($request->input('api_sources')) : null;

        // Process tag IDs
        $tagIds = null;
        if ($request->has('tag_ids')) {
            $tagIds = is_array($request->input('tag_ids'))
                ? $request->input('tag_ids')
                : explode(',', $request->input('tag_ids'));
        }

        // Process person IDs with pivot data
        $personIds = null;
        if ($request->has('person_ids')) {
            $personIds = is_array($request->input('person_ids'))
                ? $request->input('person_ids')
                : json_decode($request->input('person_ids'), true);
        }

        // Generate slug if name is provided but slug is not
        $slug = $request->input('slug');
        if (!$slug && $request->has('name')) {
            $slug = Movie::generateSlug($request->input('name'));
        }

        return new static(
            name: $request->input('name'),
            description: $request->input('description'),
            kind: $kind,
            status: $status,
            studioId: $request->input('studio_id'),
            poster: $request->input('poster'),
            backdrop: $request->input('backdrop'),
            countries: $countries,
            aliases: $aliases,
            firstAirDate: $request->input('first_air_date'),
            lastAirDate: $request->input('last_air_date'),
            duration: $request->has('duration') ? (int) $request->input('duration') : null,
            imdbScore: $request->has('imdb_score') ? (float) $request->input('imdb_score') : null,
            isPublished: $request->has('is_published') ? $request->boolean('is_published') : null,
            attachments: $attachments,
            related: $related,
            similars: $similars,
            apiSources: $apiSources,
            tagIds: $tagIds,
            personIds: $personIds,
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
     * Process attachments array from request input.
     *
     * @param mixed $input
     * @return array
     */
    private static function processAttachments($input): array
    {
        $attachments = self::processJsonArray($input);

        // Ensure all attachments have the required structure
        foreach ($attachments as $key => $attachment) {
            // Convert type to enum value if it's a string
            if (isset($attachment['type']) && is_string($attachment['type'])) {
                try {
                    $attachments[$key]['type'] = AttachmentType::from($attachment['type'])->value;
                } catch (\ValueError $e) {
                    // Invalid type, use default
                    $attachments[$key]['type'] = AttachmentType::TRAILER->value;
                }
            }

            // Ensure all required fields are present
            if (!isset($attachment['duration']) || !is_numeric($attachment['duration'])) {
                $attachments[$key]['duration'] = 0;
            }
        }

        return $attachments;
    }

    /**
     * Process related movies array from request input.
     *
     * @param mixed $input
     * @return array
     */
    private static function processRelatedMovies($input): array
    {
        $relatedMovies = self::processJsonArray($input);

        // Ensure all related movies have the required structure
        foreach ($relatedMovies as $key => $related) {
            // Convert type to enum value if it's a string
            if (isset($related['type']) && is_string($related['type'])) {
                try {
                    $relatedMovies[$key]['type'] = MovieRelateType::from($related['type'])->value;
                } catch (\ValueError $e) {
                    // Invalid type, use default
                    $relatedMovies[$key]['type'] = MovieRelateType::OTHER->value;
                }
            }
        }

        return $relatedMovies;
    }

    /**
     * Process API sources array from request input.
     *
     * @param mixed $input
     * @return array
     */
    private static function processApiSources($input): array
    {
        $apiSources = self::processJsonArray($input);

        // Ensure all API sources have the required structure
        foreach ($apiSources as $key => $source) {
            // Convert source to enum value if it's a string
            if (isset($source['source']) && is_string($source['source'])) {
                try {
                    $apiSources[$key]['source'] = ApiSourceName::from($source['source'])->value;
                } catch (\ValueError $e) {
                    // Invalid source, use default
                    $apiSources[$key]['source'] = ApiSourceName::TMDB->value;
                }
            }
        }

        return $apiSources;
    }
}
