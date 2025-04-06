<?php

use App\Http\Controllers\StudioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieNotificationsController;
use App\Http\Controllers\MovieTagController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RatingsController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserListController;

Route::apiResource('comments', CommentController::class);
Route::apiResource('comment-likes', CommentLikeController::class);
Route::apiResource('comment-reports', CommentReportController::class);
Route::apiResource('episodes', EpisodeController::class);
Route::apiResource('movies', MovieController::class);
Route::apiResource('movie-notifications', MovieNotificationsController::class);
Route::apiResource('movie-tags', MovieTagController::class);
Route::apiResource('people', PeopleController::class);
Route::apiResource('persons', PersonController::class);
Route::apiResource('ratings', RatingsController::class);
Route::apiResource('selections', SelectionController::class);
Route::apiResource('studios', StudioController::class);
Route::apiResource('tags', TagsController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('user-lists', UserListController::class);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('studios', StudioController::class);

