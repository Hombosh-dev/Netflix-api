<?php

namespace App\Http\Controllers;

use App\Actions\CommentReports\CreateCommentReportAction;
use App\Actions\CommentReports\DeleteCommentReportAction;
use App\Actions\CommentReports\ListByCommentAction;
use App\Actions\CommentReports\ListByTypeAction;
use App\Actions\CommentReports\ListByUserAction;
use App\Actions\CommentReports\ListCommentReportsAction;
use App\Actions\CommentReports\ListUnviewedAction;
use App\Actions\CommentReports\ShowCommentReportAction;
use App\Actions\CommentReports\UpdateCommentReportAction;
use App\Enums\CommentReportType;
use App\Http\Requests\CommentReport\StoreCommentReportRequest;
use App\Http\Requests\CommentReport\UpdateCommentReportRequest;
use App\Http\Resources\CommentReport\CommentReportCollection;
use App\Http\Resources\CommentReport\CommentReportResource;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class CommentReportController extends Controller
{
    public function index(ListCommentReportsAction $action)
    {
        return new CommentReportCollection($action->execute());
    }

    public function store(StoreCommentReportRequest $request, CreateCommentReportAction $action)
    {
        Gate::authorize('create', CommentReport::class);
        $data = $request->validated();
        return new CommentReportResource($action->execute($data));
    }

    public function show(CommentReport $commentReport, ShowCommentReportAction $action)
    {
        return new CommentReportResource($action->execute($commentReport));
    }

    public function update(
        CommentReport $commentReport,
        UpdateCommentReportRequest $request,
        UpdateCommentReportAction $action
    ) {
        Gate::authorize('update', $commentReport);
        $data = $request->validated();
        return new CommentReportResource($action->execute($commentReport, $data));
    }

    public function destroy(CommentReport $commentReport, DeleteCommentReportAction $action)
    {
        Gate::authorize('delete', $commentReport);
        $action->execute($commentReport);
        return response()->noContent();
    }

    public function byType(CommentReportType $type, ListByTypeAction $action)
    {
        return new CommentReportCollection($action->execute($type));
    }

    public function byUser(User $user, ListByUserAction $action)
    {
        return new CommentReportCollection($action->execute($user));
    }

    public function byComment(Comment $comment, ListByCommentAction $action)
    {
        return new CommentReportCollection($action->execute($comment));
    }

    public function unviewed(ListUnviewedAction $action)
    {
        return new CommentReportCollection($action->execute());
    }
}
