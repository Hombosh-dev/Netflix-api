<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Movies
Route::prefix('movies')->group(function () {
    Route::get('/', [MovieController::class, 'index']); // Список усіх фільмів
    Route::post('/', [MovieController::class, 'store']); // Створити фільм
    Route::get('/{movie}', [MovieController::class, 'show'])->where('movie', '.*'); // Отримати фільм (slug)
    Route::put('/{movie}', [MovieController::class, 'update'])->where('movie', '.*'); // Оновити фільм
    Route::patch('/{movie}', [MovieController::class, 'updatePartial'])->where('movie', '.*'); // Часткове оновлення
    Route::delete('/{movie}', [MovieController::class, 'destroy'])->where('movie', '.*'); // Видалити фільм

    // Фільтри за Kind (enum)
    Route::get('/kind/{kind}', [MovieController::class, 'byKind']); // Фільми за типом
    Route::get('/status/{status}', [MovieController::class, 'byStatus']); // Фільми за статусом
    Route::get('/imdb/{score}', [MovieController::class, 'byImdbScore']); // Фільми з IMDb >= score
    Route::get('/search/{query}', [MovieController::class, 'search']); // Пошук

    // Зв’язки
    Route::get('/{movie}/episodes', [MovieController::class, 'episodes'])->where('movie', '.*'); // Епізоди
    Route::get('/{movie}/ratings', [MovieController::class, 'ratings'])->where('movie', '.*'); // Рейтинги
    Route::get('/{movie}/tags', [MovieController::class, 'tags'])->where('movie', '.*'); // Теги
    Route::get('/{movie}/persons', [MovieController::class, 'persons'])->where('movie', '.*'); // Персони
    Route::get('/{movie}/comments', [MovieController::class, 'comments'])->where('movie', '.*'); // Коментарі
    Route::get('/{movie}/user-lists', [MovieController::class, 'userLists'])->where('movie', '.*'); // Списки
    Route::get('/{movie}/selections', [MovieController::class, 'selections'])->where('movie', '.*'); // Підбірки
});

// Comments
Route::prefix('comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']); // Усі коментарі
    Route::post('/', [CommentController::class, 'store']); // Додати коментар
    Route::get('/{comment}', [CommentController::class, 'show']); // Отримати коментар (ID)
    Route::put('/{comment}', [CommentController::class, 'update']); // Оновити коментар
    Route::delete('/{comment}', [CommentController::class, 'destroy']); // Видалити коментар

    // Фільтри
    Route::get('/replies', [CommentController::class, 'replies']); // Усі відповіді
    Route::get('/roots', [CommentController::class, 'roots']); // Усі кореневі коментарі

    // Зв’язки
    Route::get('/{comment}/likes', [CommentController::class, 'likes']); // Лайки
    Route::get('/{comment}/reports', [CommentController::class, 'reports']); // Скарги
    Route::get('/{comment}/children', [CommentController::class, 'children']); // Дочірні коментарі
});

// Comment Likes
Route::prefix('comment-likes')->group(function () {
    Route::get('/', [CommentLikeController::class, 'index']); // Усі лайки
    Route::post('/', [CommentLikeController::class, 'store']); // Додати лайк/дизлайк
    Route::get('/{commentLike}', [CommentLikeController::class, 'show']); // Отримати лайк (ID)
    Route::delete('/{commentLike}', [CommentLikeController::class, 'destroy']); // Видалити лайк

    // Фільтри
    Route::get('/user/{user}', [CommentLikeController::class, 'byUser']); // Лайки користувача
    Route::get('/comment/{comment}', [CommentLikeController::class, 'byComment']); // Лайки коментаря
    Route::get('/likes', [CommentLikeController::class, 'onlyLikes']); // Тільки лайки
    Route::get('/dislikes', [CommentLikeController::class, 'onlyDislikes']); // Тільки дизлайки
});

// Comment Reports
Route::prefix('comment-reports')->group(function () {
    Route::get('/', [CommentReportController::class, 'index']); // Усі скарги
    Route::post('/', [CommentReportController::class, 'store']); // Додати скаргу
    Route::get('/{commentReport}', [CommentReportController::class, 'show']); // Отримати скаргу (ID)
    Route::put('/{commentReport}', [CommentReportController::class, 'update']); // Оновити скаргу
    Route::delete('/{commentReport}', [CommentReportController::class, 'destroy']); // Видалити скаргу

    // Фільтри за CommentReportType (enum)
    Route::get('/type/{type}', [CommentReportController::class, 'byType']); // Скарги за типом
    Route::get('/user/{user}', [CommentReportController::class, 'byUser']); // Скарги від користувача
    Route::get('/comment/{comment}', [CommentReportController::class, 'byComment']); // Скарги на коментар
    Route::get('/unviewed', [CommentReportController::class, 'unviewed']); // Непереглянуті скарги
});

// Episodes
Route::prefix('episodes')->group(function () {
    Route::get('/', [EpisodeController::class, 'index']); // Усі епізоди
    Route::post('/', [EpisodeController::class, 'store']); // Додати епізод
    Route::get('/{episode}', [EpisodeController::class, 'show'])->where('episode', '.*'); // Отримати епізод (slug)
    Route::put('/{episode}', [EpisodeController::class, 'update'])->where('episode', '.*'); // Оновити епізод
    Route::delete('/{episode}', [EpisodeController::class, 'destroy'])->where('episode', '.*'); // Видалити епізод

    // Фільтри
    Route::get('/movie/{movie}', [EpisodeController::class, 'forMovie'])->where('movie', '.*'); // Епізоди для фільму
    Route::get('/aired-after/{date}', [EpisodeController::class, 'airedAfter']); // Епізоди після дати
});

// Persons
Route::prefix('persons')->group(function () {
    Route::get('/', [PersonController::class, 'index']); // Усі персони
    Route::post('/', [PersonController::class, 'store']); // Додати персону
    Route::get('/{person}', [PersonController::class, 'show'])->where('person', '.*'); // Отримати персону (slug)
    Route::put('/{person}', [PersonController::class, 'update'])->where('person', '.*'); // Оновити персону
    Route::delete('/{person}', [PersonController::class, 'destroy'])->where('person', '.*'); // Видалити персону

    // Фільтри за PersonType (enum)
    Route::get('/type/{type}', [PersonController::class, 'byType']); // Персони за типом
    Route::get('/gender/{gender}', [PersonController::class, 'byGender']); // Персони за гендером
    Route::get('/name/{name}', [PersonController::class, 'byName']); // Персони за ім’ям
    Route::get('/search/{query}', [PersonController::class, 'search']); // Пошук персон

    // Зв’язки
    Route::get('/{person}/movies', [PersonController::class, 'movies'])->where('person', '.*'); // Фільми персони
    Route::get('/{person}/user-lists', [PersonController::class, 'userLists'])->where('person', '.*'); // Списки
    Route::get('/{person}/selections', [PersonController::class, 'selections'])->where('person', '.*'); // Підбірки
});

// Ratings
Route::prefix('ratings')->group(function () {
    Route::get('/', [RatingController::class, 'index']); // Усі рейтинги
    Route::post('/', [RatingController::class, 'store']); // Додати рейтинг
    Route::get('/{rating}', [RatingController::class, 'show']); // Отримати рейтинг (ID)
    Route::put('/{rating}', [RatingController::class, 'update']); // Оновити рейтинг
    Route::delete('/{rating}', [RatingController::class, 'destroy']); // Видалити рейтинг

    // Фільтри
    Route::get('/user/{user}', [RatingController::class, 'forUser']); // Рейтинги користувача
    Route::get('/movie/{movie}', [RatingController::class, 'forMovie'])->where('movie', '.*'); // Рейтинги фільму
    Route::get('/between/{min}/{max}', [RatingController::class, 'betweenRatings']); // Рейтинги в діапазоні
});

// Selections
Route::prefix('selections')->group(function () {
    Route::get('/', [SelectionController::class, 'index']); // Усі підбірки
    Route::post('/', [SelectionController::class, 'store']); // Додати підбірку
    Route::get('/{selection}', [SelectionController::class, 'show'])->where('selection',
        '.*'); // Отримати підбірку (slug)
    Route::put('/{selection}', [SelectionController::class, 'update'])->where('selection', '.*'); // Оновити підбірку
    Route::delete('/{selection}', [SelectionController::class, 'destroy'])->where('selection',
        '.*'); // Видалити підбірку

    // Фільтри
    Route::get('/search/{query}', [SelectionController::class, 'search']); // Пошук підбірок

    // Зв’язки
    Route::get('/{selection}/movies', [SelectionController::class, 'movies'])->where('selection', '.*'); // Фільми
    Route::get('/{selection}/persons', [SelectionController::class, 'persons'])->where('selection', '.*'); // Персони
    Route::get('/{selection}/user-lists', [SelectionController::class, 'userLists'])->where('selection',
        '.*'); // Списки
    Route::get('/{selection}/comments', [SelectionController::class, 'comments'])->where('selection',
        '.*'); // Коментарі
});

// Studios
Route::prefix('studios')->group(function () {
    Route::get('/', [StudioController::class, 'index']); // Усі студії
    Route::post('/', [StudioController::class, 'store']); // Додати студію
    Route::get('/{studio}', [StudioController::class, 'show'])->where('studio', '.*'); // Отримати студію (slug)
    Route::put('/{studio}', [StudioController::class, 'update'])->where('studio', '.*'); // Оновити студію
    Route::delete('/{studio}', [StudioController::class, 'destroy'])->where('studio', '.*'); // Видалити студію

    // Фільтри
    Route::get('/name/{name}', [StudioController::class, 'byName']); // Студії за назвою
    Route::get('/search/{query}', [StudioController::class, 'search']); // Пошук студій

    // Зв’язки
    Route::get('/{studio}/movies', [StudioController::class, 'movies'])->where('studio', '.*'); // Фільми студії
});

// Tags
Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index']); // Усі теги
    Route::post('/', [TagController::class, 'store']); // Додати тег
    Route::get('/{tag}', [TagController::class, 'show'])->where('tag', '.*'); // Отримати тег (slug)
    Route::put('/{tag}', [TagController::class, 'update'])->where('tag', '.*'); // Оновити тег
    Route::delete('/{tag}', [TagController::class, 'destroy'])->where('tag', '.*'); // Видалити тег

    // Фільтри
    Route::get('/genres', [TagController::class, 'genres']); // Тільки жанри
    Route::get('/search/{term}', [TagController::class, 'search']); // Пошук тегів

    // Зв’язки
    Route::get('/{tag}/movies', [TagController::class, 'movies'])->where('tag', '.*'); // Фільми з тегом
    Route::get('/{tag}/user-lists', [TagController::class, 'userLists'])->where('tag', '.*'); // Списки з тегом
});

// Users
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']); // Усі користувачі
    Route::post('/', [UserController::class, 'store']); // Створити користувача
    Route::get('/{user}', [UserController::class, 'show']); // Отримати користувача (ID)
    Route::put('/{user}', [UserController::class, 'update']); // Оновити користувача
    Route::delete('/{user}', [UserController::class, 'destroy']); // Видалити користувача

    // Фільтри за Role (enum)
    Route::get('/role/{role}', [UserController::class, 'byRole']); // Користувачі за роллю
    Route::get('/admins', [UserController::class, 'admins']); // Тільки адміни
    Route::get('/vip', [UserController::class, 'vipCustomers']); // VIP користувачі
    Route::get('/adults', [UserController::class, 'allowedAdults']); // Дорослий контент

    // Зв’язки
    Route::get('/{user}/ratings', [UserController::class, 'ratings']); // Рейтинги
    Route::get('/{user}/comments', [UserController::class, 'comments']); // Коментарі
    Route::get('/{user}/comment-likes', [UserController::class, 'commentLikes']); // Лайки коментарів
    Route::get('/{user}/comment-reports', [UserController::class, 'commentReports']); // Скарги
    Route::get('/{user}/selections', [UserController::class, 'selections']); // Підбірки
    Route::get('/{user}/favorite-movies', [UserController::class, 'favoriteMovies']); // Улюблені фільми
    Route::get('/{user}/favorite-people', [UserController::class, 'favoritePeople']); // Улюблені персони
    Route::get('/{user}/favorite-tags', [UserController::class, 'favoriteTags']); // Улюблені теги
    Route::get('/{user}/favorite-episodes', [UserController::class, 'favoriteEpisodes']); // Улюблені епізоди
    Route::get('/{user}/watching-movies', [UserController::class, 'watchingMovies']); // Переглядаються
    Route::get('/{user}/planned-movies', [UserController::class, 'plannedMovies']); // Заплановані
    Route::get('/{user}/watched-movies', [UserController::class, 'watchedMovies']); // Переглянуті
    Route::get('/{user}/stopped-movies', [UserController::class, 'stoppedMovies']); // Закинуті
    Route::get('/{user}/rewatching-movies', [UserController::class, 'rewatchingMovies']); // Переглядаються повторно
});

// User Lists
Route::prefix('user-lists')->group(function () {
    Route::get('/', [UserListController::class, 'index']); // Усі списки
    Route::post('/', [UserListController::class, 'store']); // Додати запис
    Route::get('/{userList}', [UserListController::class, 'show']); // Отримати запис (ID)
    Route::put('/{userList}', [UserListController::class, 'update']); // Оновити запис
    Route::delete('/{userList}', [UserListController::class, 'destroy']); // Видалити запис

    // Фільтри за UserListType (enum)
    Route::get('/type/{type}', [UserListController::class, 'byType']); // Списки за типом
    Route::get('/user/{user}', [UserListController::class, 'forUser']); // Списки користувача
    Route::get('/user/{user}/type/{type}', [UserListController::class, 'forUserByType']); // Списки за типом
});


