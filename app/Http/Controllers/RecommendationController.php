<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecommendationController extends Controller
{
    /**
     * Display a listing of recommended movies.
     *
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        // In a real application, this would use a recommendation algorithm
        // For now, we'll just return the top-rated movies
        $movies = Movie::orderByDesc('imdb_score')->limit(5)->get();

        return MovieResource::collection($movies);
    }

    /**
     * Display a listing of recommended movies.
     *
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function movies(): AnonymousResourceCollection
    {
        // In a real application, this would use a recommendation algorithm
        // For now, we'll just return the top-rated movies
        $movies = Movie::where('kind', 'movie')->orderByDesc('imdb_score')->limit(5)->get();

        return MovieResource::collection($movies);
    }

    /**
     * Display a listing of recommended series.
     *
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function series(): AnonymousResourceCollection
    {
        // In a real application, this would use a recommendation algorithm
        // For now, we'll just return the top-rated series
        $series = Movie::where('kind', 'series')->orderByDesc('imdb_score')->limit(5)->get();

        return MovieResource::collection($series);
    }

    /**
     * Display a listing of similar movies to the given movie.
     *
     * @param Movie $movie
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function similar(Movie $movie): AnonymousResourceCollection
    {
        // In a real application, this would use a recommendation algorithm
        // For now, we'll just return movies with the same tags
        $similarMovies = Movie::whereHas('tags', function ($query) use ($movie) {
            $query->whereIn('tags.id', $movie->tags->pluck('id'));
        })
        ->where('id', '!=', $movie->id)
        ->orderByDesc('imdb_score')
        ->limit(5)
        ->get();

        return MovieResource::collection($similarMovies);
    }

    /**
     * Display a listing of recommended movies based on a movie the user watched.
     *
     * @param Movie $movie
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function becauseYouWatched(Movie $movie): AnonymousResourceCollection
    {
        // Similar to the similar method, but could use different algorithm
        // For now, we'll just return movies with the same tags
        $recommendedMovies = Movie::whereHas('tags', function ($query) use ($movie) {
            $query->whereIn('tags.id', $movie->tags->pluck('id'));
        })
        ->where('id', '!=', $movie->id)
        ->orderByDesc('imdb_score')
        ->limit(5)
        ->get();

        return MovieResource::collection($recommendedMovies);
    }

    /**
     * Display a listing of movies the user was watching but hasn't finished.
     *
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function continueWatching(): AnonymousResourceCollection
    {
        // In a real application, this would fetch the user's watch history
        // For now, we'll just return some random movies
        $movies = Movie::inRandomOrder()->limit(5)->get();

        return MovieResource::collection($movies);
    }
}
