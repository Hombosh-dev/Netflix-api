<?php

namespace App\Http\Controllers;

use App\Actions\Studio\CreateStudioAction;
use App\Actions\Studio\DeleteStudioAction;
use App\Actions\Studio\ReadStudioAction;
use App\Actions\Studio\UpdateStudioAction;
use App\Http\Requests\Studio\CreateStudioRequest;
use App\Http\Requests\Studio\UpdateStudioRequest;
use App\Models\Studio;
use App\Http\Resources\StudioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudioController extends Controller
{
    /**
     * Display a listing of the studios.
     */
    public function index(): JsonResponse
    {
        return response()->json(Studio::all());
    }

    /**
     * Store a newly created studio in storage.
     */
    public function store(CreateStudioRequest $request, CreateStudioAction $createStudio): StudioResource
    {
        $studio = $createStudio->execute($request->validated());

        return new StudioResource($studio);
    }

    /**
     * Display the specified studio.
     */
    public function show($id, ReadStudioAction $getStudio): JsonResponse | StudioResource
    {
        $studio = $getStudio->execute($id);

        if (!$studio) {
            return response()->json(['message' => 'Studio not found'], ResponseAlias::HTTP_NOT_FOUND);
        }

        return new StudioResource($studio);
    }

    /**
     * Update the specified studio in storage.
     */
    public function update(UpdateStudioRequest $request, Studio $studio, UpdateStudioAction $updateStudio):  Studio
    {
        $updateStudio->execute($studio, $request->validated());

        return new Studio((array)$studio);
    }

    /**
     * Remove the specified studio from storage.
     */
    public function destroy(Studio $studio, DeleteStudioAction $deleteStudio): JsonResponse
    {
        $deleteStudio->execute($studio);

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}

