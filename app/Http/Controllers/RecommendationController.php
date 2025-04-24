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
     */
    public function index(): AnonymousResourceCollection
    {
        // In a real application, this would use a recommendation algorithm
        // For now, we'll just return the top-rated movies
        $movies = Movie::orderByDesc('imdb_score')->limit(5)->get();
        
        return MovieResource::collection($movies);
    }
}
