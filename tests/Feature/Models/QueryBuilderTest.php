<?php

use App\Models\Builders\CommentQueryBuilder;
use App\Models\Builders\EpisodeQueryBuilder;
use App\Models\Builders\PersonQueryBuilder;
use App\Models\Builders\StudioQueryBuilder;
use App\Models\Builders\TagQueryBuilder;
use App\Models\Builders\UserListQueryBuilder;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\UserList;

test('all models have correct query builders', function () {
    expect(Comment::query())->toBeInstanceOf(CommentQueryBuilder::class)
        ->and(Episode::query())->toBeInstanceOf(EpisodeQueryBuilder::class)
        ->and(Person::query())->toBeInstanceOf(PersonQueryBuilder::class)
        ->and(Studio::query())->toBeInstanceOf(StudioQueryBuilder::class)
        ->and(Tag::query())->toBeInstanceOf(TagQueryBuilder::class)
        ->and(UserList::query())->toBeInstanceOf(UserListQueryBuilder::class);
});

test('comment query builder can get replies', function () {
    $query = Comment::query()->replies();
    $sql = $query->toSql();

    expect($sql)->toContain('"parent_id" is not null');
});

test('comment query builder can get root comments', function () {
    $query = Comment::query()->roots();
    $sql = $query->toSql();

    expect($sql)->toContain('"parent_id" is null');
});

test('episode query builder can filter by movie', function () {
    $movieId = 'test-movie-id';
    $query = Episode::query()->forMovie($movieId);
    $sql = $query->toSql();

    expect($sql)->toContain('"movie_id" = ?')
        ->and($query->getBindings())->toContain($movieId);
});

test('episode query builder can filter fillers', function () {
    $query = Episode::query()->fillers(false);
    $sql = $query->toSql();

    expect($sql)->toContain('"is_filler" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('person query builder can get actors', function () {
    $query = Person::query()->actors();
    $sql = $query->toSql();

    expect($sql)->toContain('"type" = ?');
});

test('person query builder can get directors', function () {
    $query = Person::query()->directors();
    $sql = $query->toSql();

    expect($sql)->toContain('"type" = ?');
});

test('studio query builder can filter by name', function () {
    $query = Studio::query()->byName('Warner');
    $sql = $query->toSql();

    expect($sql)->toContain('"name"')
        ->and($sql)->toContain('like ?')
        ->and($query->getBindings())->toContain('%Warner%');
});

test('tag query builder can filter genres', function () {
    $query = Tag::query()->genres();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_genre" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('tag query builder can filter non-genres', function () {
    $query = Tag::query()->nonGenres();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_genre" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('user list query builder can get favorites', function () {
    $query = UserList::query()->favorites();
    $sql = $query->toSql();

    expect($sql)->toContain('"type" = ?');
});
