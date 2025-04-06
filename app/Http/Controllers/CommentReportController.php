<?php

namespace App\Http\Controllers;

use App\Actions\CommentReport\CreateCommentReportAction;
use App\Actions\CommentReport\DeleteCommentReportAction;
use App\Actions\CommentReport\ReadCommentReportAction;
use App\Http\Requests\CommentReport\CreateCommentReportRequest;
use App\Http\Requests\CommentReport\UpdateCommentReportRequest;
use App\Http\Resources\CommentReportResource;
use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentReportController extends Controller
{
    /**
     * Повертає колекцію всіх записів CommentReport.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $reports = CommentReport::all();
        return CommentReportResource::collection($reports);
    }

    /**
     * Зберігає новий запис CommentReport.
     */
    public function store(CreateCommentReportRequest $request, CreateCommentReportAction $createAction): CommentReportResource
    {
        $report = $createAction->execute($request->validated());
        return new CommentReportResource($report);
    }

    /**
     * Повертає дані конкретного CommentReport.
     */
    public function show(CommentReport $commentReport, ReadCommentReportAction $readAction): CommentReportResource
    {
        return new CommentReportResource($commentReport);
    }

    /**
     * Оновлює дані конкретного CommentReport.
     */
    public function update(UpdateCommentReportRequest $request, CommentReport $commentReport, UpdateCommentReportAction $updateAction): CommentReportResource
    {
        $updateAction->execute($commentReport, $request->validated());
        return new CommentReportResource($commentReport);
    }

    /**
     * Видаляє запис CommentReport.
     */
    public function destroy(CommentReport $commentReport, DeleteCommentReportAction $deleteAction): \Illuminate\Http\Response
    {
        $deleteAction->execute($commentReport);
        return response()->noContent();
    }
}
