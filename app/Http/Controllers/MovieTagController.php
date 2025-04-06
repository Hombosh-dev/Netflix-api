<?php

namespace App\Http\Controllers;

use App\Actions\MovieTag\CreateMovieTagAction;
use App\Actions\MovieTag\DeleteMovieTagAction;
use App\Actions\MovieTag\ReadMovieTagAction;
use App\Actions\MovieTag\UpdateMovieTagAction;
use App\Http\Requests\MovieTag\CreateMovieTagRequest;
use App\Http\Requests\MovieTag\UpdateMovieTagRequest;
use App\Http\Resources\MovieTagResource;
use App\Models\MovieTag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MovieTagController extends Controller
{
    /**
     * Повертає колекцію всіх записів MovieTag.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $movieTags = MovieTag::all();
        return MovieTagResource::collection($movieTags);
    }

    /**
     * Створює новий запис MovieTag.
     * @throws AuthorizationException
     */
    public function store(CreateMovieTagRequest $request, CreateMovieTagAction $createAction): MovieTagResource
    {
        $movieTag = $createAction->execute($request->validated());
        return new MovieTagResource($movieTag);
    }

    /**
     * Повертає дані конкретного MovieTag.
     *
     * Note: Here we assume that the route parameters provide both movie_id and tag_id,
     * or you can use Route Model Binding with a composite key if set up.
     * @throws AuthorizationException
     */
    public function show(ReadMovieTagAction $readAction, string $movie_id, string $tag_id): MovieTagResource
    {
        $movieTag = $readAction->execute($movie_id, $tag_id);
        return new MovieTagResource($movieTag);
    }

    /**
     * Оновлює дані конкретного MovieTag.
     * @throws AuthorizationException
     */
    public function update(UpdateMovieTagRequest $request, MovieTag $movieTag, UpdateMovieTagAction $updateAction): MovieTagResource
    {
        $updateAction->execute($movieTag, $request->validated());
        return new MovieTagResource($movieTag);
    }

    /**
     * Видаляє запис MovieTag.
     * @throws AuthorizationException
     */
    public function destroy(MovieTag $movieTag, DeleteMovieTagAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($movieTag);
        return response()->noContent();
    }
}
