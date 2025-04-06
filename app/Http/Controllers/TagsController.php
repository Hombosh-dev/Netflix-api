<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTagsAction;
use App\Actions\Tags\DeleteTagsAction;
use App\Actions\Tags\ReadTagsAction;
use App\Actions\Tags\UpdateTagsAction;
use App\Http\Requests\Tags\CreateTagsRequest;
use App\Http\Requests\Tags\UpdateTagsRequest;
use App\Http\Resources\TagsResource;
use App\Models\Tags;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Повертає колекцію всіх записів Tags.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $tags = Tags::all();
        return TagsResource::collection($tags);
    }

    /**
     * Створює новий запис Tags.
     * @throws AuthorizationException
     */
    public function store(CreateTagsRequest $request, CreateTagsAction $createAction): TagsResource
    {
        $tag = $createAction->execute($request->validated());
        return new TagsResource($tag);
    }

    /**
     * Повертає дані конкретного Tags.
     */
    public function show(Tags $tag, ReadTagsAction $readAction): TagsResource
    {
        return new TagsResource($tag);
    }

    /**
     * Оновлює дані конкретного Tags.
     * @throws AuthorizationException
     */
    public function update(UpdateTagsRequest $request, Tags $tag, UpdateTagsAction $updateAction): TagsResource
    {
        $updateAction->execute($tag, $request->validated());
        return new TagsResource($tag);
    }

    /**
     * Видаляє запис Tags.
     * @throws AuthorizationException
     */
    public function destroy(Tags $tag, DeleteTagsAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($tag);
        return response()->noContent();
    }
}
