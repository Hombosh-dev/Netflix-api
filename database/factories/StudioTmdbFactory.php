<?php

namespace Database\Factories;

use App\Enums\ApiSourceName;
use App\Models\Studio;
use App\Services\FileService;
use App\Services\TmdbApiService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Studio>
 */
class StudioTmdbFactory extends Factory
{
    /**
     * The TMDB API service instance.
     */
    protected TmdbApiService $tmdbService;

    /**
     * The file service instance.
     */
    protected FileService $fileService;

    /**
     * Create a new factory instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->tmdbService = app(TmdbApiService::class);
        $this->fileService = app(FileService::class);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default state with placeholder values
        return [
            'name' => fake()->company(),
            'slug' => '',
            'description' => fake()->paragraph(3),
            'image' => null,
            'meta_title' => '',
            'meta_description' => fake()->sentence(10),
            'meta_image' => null,
            'aliases' => [],
            'api_sources' => [
                [
                    'source' => ApiSourceName::TMDB->value,
                    'id' => null
                ]
            ],
        ];
    }

    /**
     * Configure the studio with data from TMDB by name.
     *
     * @param string $name The studio name to search for
     * @return static
     */
    public function fromTmdbByName(string $name): static
    {
        return $this->state(function (array $attributes) use ($name) {
            $studioData = $this->tmdbService->searchStudio($name);
            
            if (!$studioData) {
                Log::warning("Studio not found on TMDB: {$name}");
                return [
                    'name' => $name,
                    'slug' => Studio::generateSlug($name),
                    'meta_title' => Studio::makeMetaTitle($name),
                ];
            }
            
            return $this->processStudioData($studioData);
        });
    }

    /**
     * Configure the studio with data from TMDB by ID.
     *
     * @param int $tmdbId The TMDB ID of the studio
     * @return static
     */
    public function fromTmdbById(int $tmdbId): static
    {
        return $this->state(function (array $attributes) use ($tmdbId) {
            $studioData = $this->tmdbService->getStudioDetails($tmdbId);
            
            if (!$studioData) {
                Log::warning("Studio not found on TMDB with ID: {$tmdbId}");
                return $attributes;
            }
            
            return $this->processStudioData($studioData);
        });
    }

    /**
     * Process studio data from TMDB and prepare it for the model.
     *
     * @param array $studioData
     * @return array
     */
    protected function processStudioData(array $studioData): array
    {
        $name = $studioData['name'];
        $description = $studioData['description'] ?? null;
        $logoPath = $studioData['logo_path'] ?? null;
        $tmdbId = $studioData['id'];
        
        // Download and store the logo if available
        $image = null;
        if ($logoPath) {
            $image = $this->downloadAndStoreImage(
                $this->tmdbService->getLogoUrl($logoPath),
                'studios'
            );
        }
        
        // Use the same image for meta image if available
        $metaImage = $image;
        
        // Generate aliases from alternative names if available
        $aliases = [];
        if (isset($studioData['alternative_names'])) {
            $aliases = $studioData['alternative_names'];
        }
        
        return [
            'name' => $name,
            'slug' => Studio::generateSlug($name),
            'description' => $description ?: fake()->paragraph(3),
            'image' => $image,
            'meta_title' => Studio::makeMetaTitle($name),
            'meta_description' => $description ?: fake()->sentence(10),
            'meta_image' => $metaImage,
            'aliases' => $aliases,
            'api_sources' => [
                [
                    'source' => ApiSourceName::TMDB->value,
                    'id' => (string) $tmdbId
                ]
            ],
        ];
    }

    /**
     * Download an image from URL and store it locally.
     *
     * @param string|null $url
     * @param string $directory
     * @return string|null
     */
    protected function downloadAndStoreImage(?string $url, string $directory): ?string
    {
        if (!$url) {
            return null;
        }
        
        try {
            // Download the image
            $imageContent = $this->tmdbService->downloadImage($url);
            
            if (!$imageContent) {
                return null;
            }
            
            // Get file extension from URL
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            
            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'tmdb_');
            file_put_contents($tempFile, $imageContent);
            
            // Create an UploadedFile instance
            $uploadedFile = new UploadedFile(
                $tempFile,
                Str::uuid() . '.' . $extension,
                mime_content_type($tempFile),
                null,
                true
            );
            
            // Store the file using the FileService
            $filePath = $this->fileService->storeFile($uploadedFile, $directory);
            
            // Clean up the temporary file
            @unlink($tempFile);
            
            return $filePath;
        } catch (\Exception $e) {
            Log::error("Failed to download and store image: {$e->getMessage()}");
            return null;
        }
    }
}
