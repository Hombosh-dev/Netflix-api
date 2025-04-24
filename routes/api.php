<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PopularController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserSubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Public routes
Route::group(['prefix' => 'v1'], function () {
    // Authentication routes
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    // Public content routes
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/autocomplete', [SearchController::class, 'autocomplete']);

    // Popular content
    Route::get('/popular/movies', [PopularController::class, 'movies']);
    Route::get('/popular/series', [PopularController::class, 'series']);
    Route::get('/popular/people', [PopularController::class, 'people']);
    Route::get('/popular/tags', [PopularController::class, 'tags']);
    Route::get('/popular/selections', [PopularController::class, 'selections']);

    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{movie}', [MovieController::class, 'show']);
    Route::get('/movies/{movie}/episodes', [MovieController::class, 'episodes']);
    Route::get('/movies/{movie}/persons', [MovieController::class, 'persons']);
    Route::get('/movies/{movie}/tags', [MovieController::class, 'tags']);
    Route::get('/movies/{movie}/ratings', [MovieController::class, 'ratings']);
    Route::get('/movies/{movie}/comments', [MovieController::class, 'comments']);

    Route::get('/episodes', [EpisodeController::class, 'index']);
    Route::get('/episodes/aired-after/{date}', [EpisodeController::class, 'airedAfter']);
    Route::get('/episodes/{episode}', [EpisodeController::class, 'show']);
    Route::get('/episodes/movie/{movie}', [EpisodeController::class, 'forMovie']);

    Route::get('/people', [PersonController::class, 'index']);
    Route::get('/people/{person}', [PersonController::class, 'show']);
    Route::get('/people/{person}/movies', [PersonController::class, 'movies']);

    Route::get('/studios', [StudioController::class, 'index']);
    Route::get('/studios/{studio}', [StudioController::class, 'show']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::get('/tags/{tag}/movies', [TagController::class, 'movies']);

    Route::get('/selections', [SelectionController::class, 'index']);
    Route::get('/selections/{selection}', [SelectionController::class, 'show']);
    Route::get('/selections/{selection}/movies', [SelectionController::class, 'movies']);
    Route::get('/selections/{selection}/persons', [SelectionController::class, 'persons']);

    // Comments
    Route::get('/comments/recent', [CommentController::class, 'recent']);
    Route::get('/comments/roots/{commentable_type}/{commentable_id}', [CommentController::class, 'roots']);

    // Enum routes with SEO
    Route::prefix('enums')->group(function () {
        Route::get('/kinds', [EnumController::class, 'kinds']);
        Route::get('/kinds/{kind}', [EnumController::class, 'kind']);

        Route::get('/statuses', [EnumController::class, 'statuses']);
        Route::get('/statuses/{status}', [EnumController::class, 'status']);

        Route::get('/person-types', [EnumController::class, 'personTypes']);
        Route::get('/person-types/{type}', [EnumController::class, 'personType']);

        Route::get('/user-list-types', [EnumController::class, 'userListTypes']);
        Route::get('/user-list-types/{type}', [EnumController::class, 'userListType']);

        Route::get('/video-qualities', [EnumController::class, 'videoQualities']);
        Route::get('/video-qualities/{quality}', [EnumController::class, 'videoQuality']);

        Route::get('/genders', [EnumController::class, 'genders']);
        Route::get('/genders/{gender}', [EnumController::class, 'gender']);

        Route::get('/comment-report-types', [EnumController::class, 'commentReportTypes']);
        Route::get('/comment-report-types/{type}', [EnumController::class, 'commentReportType']);

        Route::get('/movie-relate-types', [EnumController::class, 'movieRelateTypes']);
        Route::get('/movie-relate-types/{type}', [EnumController::class, 'movieRelateType']);

        Route::get('/payment-statuses', [EnumController::class, 'paymentStatuses']);
        Route::get('/payment-statuses/{status}', [EnumController::class, 'paymentStatus']);

        Route::get('/api-source-names', [EnumController::class, 'apiSourceNames']);
        Route::get('/api-source-names/{source}', [EnumController::class, 'apiSourceName']);

        Route::get('/attachment-types', [EnumController::class, 'attachmentTypes']);
        Route::get('/attachment-types/{type}', [EnumController::class, 'attachmentType']);
    });
});

// Protected routes (require authentication)
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    // User verification
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::get('/recommendations/movies', [RecommendationController::class, 'movies']);
    Route::get('/recommendations/series', [RecommendationController::class, 'series']);
    Route::get('/recommendations/similar/{movie}', [RecommendationController::class, 'similar']);
    Route::get('/recommendations/because-you-watched/{movie}', [RecommendationController::class, 'becauseYouWatched']);
    Route::get('/recommendations/continue-watching', [RecommendationController::class, 'continueWatching']);

    // Users
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}', [UserController::class, 'updatePartial']);
    Route::get('/users/{user}/user-lists', [UserController::class, 'userLists']);
    Route::get('/users/{user}/ratings', [UserController::class, 'ratings']);
    Route::get('/users/{user}/comments', [UserController::class, 'comments']);
    Route::get('/users/{user}/subscriptions', [UserController::class, 'subscriptions']);

    // User lists (favorites, watch later, etc.)
    Route::get('/user-lists', [UserListController::class, 'index']);
    Route::post('/user-lists', [UserListController::class, 'store']);
    Route::get('/user-lists/{userList}', [UserListController::class, 'show']);
    Route::delete('/user-lists/{userList}', [UserListController::class, 'destroy']);
    Route::get('/user-lists/user/{user}', [UserListController::class, 'forUser']);
    Route::get('/user-lists/type/{type}', [UserListController::class, 'byType']);

    // Ratings
    Route::get('/ratings', [RatingController::class, 'index']);
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::get('/ratings/{rating}', [RatingController::class, 'show']);
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
    Route::get('/ratings/user/{user}', [RatingController::class, 'forUser']);
    Route::get('/ratings/movie/{movie}', [RatingController::class, 'forMovie']);

    // Comments
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('/comments/user/{user}', [CommentController::class, 'forUser']);
    Route::get('/comments/{comment}/replies', [CommentController::class, 'replies']);

    // Comment likes
    Route::get('/comment-likes', [CommentLikeController::class, 'index']);
    Route::post('/comment-likes', [CommentLikeController::class, 'store']);
    Route::get('/comment-likes/comment/{comment}', [CommentLikeController::class, 'forComment']);
    Route::get('/comment-likes/user/{user}', [CommentLikeController::class, 'forUser']);
    Route::get('/comment-likes/{commentLike}', [CommentLikeController::class, 'show']);
    Route::put('/comment-likes/{commentLike}', [CommentLikeController::class, 'update']);
    Route::delete('/comment-likes/{commentLike}', [CommentLikeController::class, 'destroy']);

    // Comment reports
    Route::post('/comment-reports', [CommentReportController::class, 'store']);
    Route::get('/comment-reports/comment/{comment}', [CommentReportController::class, 'forComment']);

    // Subscriptions and payments
    Route::get('/tariffs', [TariffController::class, 'index']);
    Route::get('/tariffs/{tariff}', [TariffController::class, 'show']);

    Route::get('/user-subscriptions', [UserSubscriptionController::class, 'index']);
    Route::post('/user-subscriptions', [UserSubscriptionController::class, 'store']);
    Route::get('/user-subscriptions/{userSubscription}', [UserSubscriptionController::class, 'show']);
    Route::get('/user-subscriptions/user/{user}', [UserSubscriptionController::class, 'forUser']);
    Route::get('/user-subscriptions/active', [UserSubscriptionController::class, 'active']);

    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::get('/payments/user/{user}', [PaymentController::class, 'forUser']);
});

// Admin routes
Route::group(['prefix' => 'v1/admin', 'middleware' => ['auth:sanctum', 'admin']], function () {
    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::patch('/users/{user}/ban', [UserController::class, 'ban']);
    Route::patch('/users/{user}/unban', [UserController::class, 'unban']);

    // Content management
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{movie}', [MovieController::class, 'update']);
    Route::patch('/movies/{movie}', [MovieController::class, 'updatePartial']);
    Route::delete('/movies/{movie}', [MovieController::class, 'destroy']);

    Route::post('/episodes', [EpisodeController::class, 'store']);
    Route::put('/episodes/{episode}', [EpisodeController::class, 'update']);
    Route::delete('/episodes/{episode}', [EpisodeController::class, 'destroy']);

    Route::post('/people', [PersonController::class, 'store']);
    Route::put('/people/{person}', [PersonController::class, 'update']);
    Route::delete('/people/{person}', [PersonController::class, 'destroy']);

    Route::post('/studios', [StudioController::class, 'store']);
    Route::put('/studios/{studio}', [StudioController::class, 'update']);
    Route::delete('/studios/{studio}', [StudioController::class, 'destroy']);

    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

    Route::post('/selections', [SelectionController::class, 'store']);
    Route::put('/selections/{selection}', [SelectionController::class, 'update']);
    Route::delete('/selections/{selection}', [SelectionController::class, 'destroy']);

    // Moderation
    Route::get('/comment-reports', [CommentReportController::class, 'index']);
    Route::get('/comment-reports/unviewed', [CommentReportController::class, 'unviewed']);
    Route::get('/comment-reports/{commentReport}', [CommentReportController::class, 'show']);
    Route::put('/comment-reports/{commentReport}', [CommentReportController::class, 'update']);
    Route::delete('/comment-reports/{commentReport}', [CommentReportController::class, 'destroy']);

    // Subscription management
    Route::post('/tariffs', [TariffController::class, 'store']);
    Route::put('/tariffs/{tariff}', [TariffController::class, 'update']);
    Route::delete('/tariffs/{tariff}', [TariffController::class, 'destroy']);

    // Statistics and reports
    Route::get('/stats/users', [StatsController::class, 'users']);
    Route::get('/stats/content', [StatsController::class, 'content']);
    Route::get('/stats/subscriptions', [StatsController::class, 'subscriptions']);
    Route::get('/stats/payments', [StatsController::class, 'payments']);
});
