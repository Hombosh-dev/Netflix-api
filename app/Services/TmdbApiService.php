<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TmdbApiService
{
    private string $baseUrl = 'https://api.themoviedb.org/3';
    private string $apiKey;
    private string $language = 'uk-UA';
    private string $imageBaseUrl = 'https://image.tmdb.org/t/p/';

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY', '');
        
        if (empty($this->apiKey)) {
            Log::error('TMDB API key is not set in .env file');
        }
    }

    /**
     * Search for a studio by name
     *
     * @param string $query
     * @return array|null
     */
    public function searchStudio(string $query): ?array
    {
        $cacheKey = "tmdb_studio_search_{$query}";
        
        return Cache::remember($cacheKey, 3600, function () use ($query) {
            $response = Http::get("{$this->baseUrl}/search/company", [
                'api_key' => $this->apiKey,
                'language' => $this->language,
                'query' => $query,
            ]);
            
            if ($response->successful() && isset($response->json()['results'][0])) {
                return $response->json()['results'][0];
            }
            
            return null;
        });
    }

    /**
     * Get studio details by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getStudioDetails(int $id): ?array
    {
        $cacheKey = "tmdb_studio_{$id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($id) {
            $response = Http::get("{$this->baseUrl}/company/{$id}", [
                'api_key' => $this->apiKey,
                'language' => $this->language,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }

    /**
     * Get studio logo URL
     *
     * @param string|null $path
     * @param string $size
     * @return string|null
     */
    public function getLogoUrl(?string $path, string $size = 'w500'): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        return "{$this->imageBaseUrl}{$size}{$path}";
    }

    /**
     * Get movies by studio ID
     *
     * @param int $id
     * @param int $page
     * @return array
     */
    public function getStudioMovies(int $id, int $page = 1): array
    {
        $cacheKey = "tmdb_studio_movies_{$id}_{$page}";
        
        return Cache::remember($cacheKey, 3600, function () use ($id, $page) {
            $response = Http::get("{$this->baseUrl}/discover/movie", [
                'api_key' => $this->apiKey,
                'language' => $this->language,
                'with_companies' => $id,
                'page' => $page,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return ['results' => []];
        });
    }

    /**
     * Download an image from URL and return the file contents
     *
     * @param string $url
     * @return string|null
     */
    public function downloadImage(string $url): ?string
    {
        $response = Http::get($url);
        
        if ($response->successful()) {
            return $response->body();
        }
        
        return null;
    }
}
