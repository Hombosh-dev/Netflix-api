<?php

namespace App\Http\Controllers;

use App\Actions\Movie\CreateMovieAction;
use App\Actions\Movie\DeleteMovieAction;
use App\Actions\Movie\ReadMovieAction;
use App\Actions\Movie\UpdateMovieAction;
use App\Http\Requests\Movie\CreateMovieRequest;
use App\Http\Requests\Movie\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Повертає колекцію всіх записів Movie.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $movies = Movie::all();

        return MovieResource::collection($movies);
    }

    /**
     * Зберігає новий запис Movie.
     */
    public function store(CreateMovieRequest $request, CreateMovieAction $createMovie): MovieResource
    {
        $movie = $createMovie->execute($request->validated());

        return new MovieResource($movie);
    }

    /**
     * Повертає дані конкретного Movie.
     */
    public function show(Movie $movie, ReadMovieAction $getMovie): MovieResource
    {
        return new MovieResource($movie);
    }

    /**
     * Оновлює дані конкретного Movie.
     * @throws AuthorizationException
     */
    public function update(UpdateMovieRequest $request, Movie $movie, UpdateMovieAction $updateMovie): MovieResource
    {
        $updateMovie->execute($movie, $request->validated());

        return new MovieResource($movie);
    }

    /**
     * Видаляє запис Movie.
     * @throws AuthorizationException
     */
    public function destroy(Movie $movie, DeleteMovieAction $deleteMovie): \Illuminate\Http\Response
    {
        $deleteMovie->execute($movie);

        return response()->noContent();
    }
}
