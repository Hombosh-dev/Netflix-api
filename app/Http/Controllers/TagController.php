<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTagAction;
use App\Actions\Tags\DeleteTagAction;
use App\Actions\Tags\ReadTagAction;
use App\Actions\Tags\UpdateTagAction;
use App\Http\Requests\Tags\CreateTagsRequest;
use App\Http\Requests\Tags\UpdateTagsRequest;
use App\Http\Resources\TagsResource;
use App\Models\Tag;
use Illuminate\Auth\Access\AuthorizationException;

class TagController extends Controller
{
    /**
     * Повертає колекцію всіх записів Tags.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $tags = Tag::all();
        return TagsResource::collection($tags);
    }

    /**
     * Створює новий запис Tags.
     * @throws AuthorizationException
     */
    public function store(CreateTagsRequest $request, CreateTagAction $createAction): TagsResource
    {
        $tag = $createAction->execute($request->validated());
        return new TagsResource($tag);
    }

    /**
     * Повертає дані конкретного Tags.
     */
    public function show(Tag $tag, ReadTagAction $readAction): TagsResource
    {
        return new TagsResource($tag);
    }

    /**
     * Оновлює дані конкретного Tags.
     * @throws AuthorizationException
     */
    public function update(UpdateTagsRequest $request, Tag $tag, UpdateTagAction $updateAction): TagsResource
    {
        $updateAction->execute($tag, $request->validated());
        return new TagsResource($tag);
    }

    /**
     * Видаляє запис Tags.
     * @throws AuthorizationException
     */
    public function destroy(Tag $tag, DeleteTagAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($tag);
        return response()->noContent();
    }
}
