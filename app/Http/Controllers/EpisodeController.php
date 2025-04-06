<?php

namespace App\Http\Controllers;

use App\Actions\Episode\CreateEpisodeAction;
use App\Actions\Episode\DeleteEpisodeAction;
use App\Actions\Episode\ReadEpisodeAction;
use App\Actions\Episode\UpdateEpisodeAction;
use App\Http\Requests\Episode\CreateEpisodeRequest;
use App\Http\Requests\Episode\UpdateEpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    /**
     * Повертає колекцію всіх записів Episode.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $episodes = Episode::all();
        return EpisodeResource::collection($episodes);
    }

    /**
     * Створює новий запис Episode.
     */
    public function store(CreateEpisodeRequest $request, CreateEpisodeAction $createEpisodeAction): EpisodeResource
    {
        $episode = $createEpisodeAction->execute($request->validated());
        return new EpisodeResource($episode);
    }

    /**
     * Повертає дані конкретного Episode.
     */
    public function show(Episode $episode, ReadEpisodeAction $readEpisodeAction): EpisodeResource
    {
        return new EpisodeResource($episode);
    }

    /**
     * Оновлює дані конкретного Episode.
     */
    public function update(UpdateEpisodeRequest $request, Episode $episode, UpdateEpisodeAction $updateEpisodeAction): EpisodeResource
    {
        $updateEpisodeAction->execute($episode, $request->validated());
        return new EpisodeResource($episode);
    }

    /**
     * Видаляє запис Episode.
     */
    public function destroy(Episode $episode, DeleteEpisodeAction $deleteEpisodeAction): \Illuminate\Http\Response
    {
        $deleteEpisodeAction->execute($episode);
        return response()->noContent();
    }
}
