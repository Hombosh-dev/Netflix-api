<?php

namespace App\Http\Controllers;

use App\Actions\CommentReports\CreateCommentReport;
use App\Actions\CommentReports\GetCommentReports;
use App\Actions\CommentReports\UpdateCommentReport;
use App\DTOs\CommentReports\CommentReportIndexDTO;
use App\DTOs\CommentReports\CommentReportStoreDTO;
use App\DTOs\CommentReports\CommentReportUpdateDTO;
use App\Http\Requests\CommentReports\CommentReportDeleteRequest;
use App\Http\Requests\CommentReports\CommentReportIndexRequest;
use App\Http\Requests\CommentReports\CommentReportStoreRequest;
use App\Http\Requests\CommentReports\CommentReportUpdateRequest;
use App\Http\Resources\CommentReportResource;
use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentReportController extends Controller
{
    /**
     * Get paginated list of comment reports with filtering, sorting and pagination
     *
     * @param  CommentReportIndexRequest  $request
     * @param  GetCommentReports  $action
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function index(CommentReportIndexRequest $request, GetCommentReports $action): AnonymousResourceCollection
    {
        $dto = CommentReportIndexDTO::fromRequest($request);
        $commentReports = $action->handle($dto);

        return CommentReportResource::collection($commentReports);
    }

    /**
     * Get detailed information about a specific comment report
     *
     * @param  CommentReport  $commentReport
     * @return CommentReportResource
     * @authenticated
     */
    public function show(CommentReport $commentReport): CommentReportResource
    {
        return new CommentReportResource($commentReport->load(['user', 'comment']));
    }

    /**
     * Store a newly created comment report
     *
     * @param  CommentReportStoreRequest  $request
     * @param  CreateCommentReport  $action
     * @return CommentReportResource|JsonResponse
     * @authenticated
     */
    public function store(CommentReportStoreRequest $request, CreateCommentReport $action): CommentReportResource|JsonResponse
    {
        $dto = CommentReportStoreDTO::fromRequest($request);

        // Check if the user has already reported this comment with the same type
        $existingReport = CommentReport::where('user_id', $request->user()->id)
            ->where('comment_id', $request->input('comment_id'))
            ->where('type', $request->input('type'))
            ->first();

        if ($existingReport) {
            return response()->json(['message' => 'You have already reported this comment for this reason'], 422);
        }

        $commentReport = $action->handle($dto);

        return new CommentReportResource($commentReport->load(['user', 'comment']));
    }

    /**
     * Update the specified comment report
     *
     * @param  CommentReportUpdateRequest  $request
     * @param  CommentReport  $commentReport
     * @param  UpdateCommentReport  $action
     * @return CommentReportResource
     * @authenticated
     */
    public function update(CommentReportUpdateRequest $request, CommentReport $commentReport, UpdateCommentReport $action): CommentReportResource
    {
        $dto = CommentReportUpdateDTO::fromRequest($request);
        $commentReport = $action->handle($commentReport, $dto);

        return new CommentReportResource($commentReport->load(['user', 'comment']));
    }

    /**
     * Remove the specified comment report
     *
     * @param  CommentReportDeleteRequest  $request
     * @param  CommentReport  $commentReport
     * @return JsonResponse
     * @authenticated
     */
    public function destroy(CommentReportDeleteRequest $request, CommentReport $commentReport): JsonResponse
    {
        $commentReport->delete();

        return response()->json(['message' => 'Report removed successfully']);
    }

    /**
     * Get reports for a specific comment
     *
     * @param  Comment  $comment
     * @param  CommentReportIndexRequest  $request
     * @param  GetCommentReports  $action
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function forComment(Comment $comment, CommentReportIndexRequest $request, GetCommentReports $action): AnonymousResourceCollection
    {
        $request->merge(['comment_id' => $comment->id]);
        $dto = CommentReportIndexDTO::fromRequest($request);
        $commentReports = $action->handle($dto);

        return CommentReportResource::collection($commentReports);
    }

    /**
     * Get unviewed reports
     *
     * @param  CommentReportIndexRequest  $request
     * @param  GetCommentReports  $action
     * @return AnonymousResourceCollection
     * @authenticated
     */
    public function unviewed(CommentReportIndexRequest $request, GetCommentReports $action): AnonymousResourceCollection
    {
        $request->merge(['is_viewed' => false]);
        $dto = CommentReportIndexDTO::fromRequest($request);
        $commentReports = $action->handle($dto);

        return CommentReportResource::collection($commentReports);
    }
}
