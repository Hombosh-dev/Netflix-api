<?php

use App\Models\Movie;
use App\Models\Person;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;

test('movie query builder can get popular movies', function () {
    $query = Movie::query()->popular();
    $sql = $query->toSql();

    expect($sql)->toContain('order by')
        ->and($sql)->toContain('desc');
});

test('movie query builder can get trending movies', function () {
    $query = Movie::query()->trending(7);
    $sql = $query->toSql();

    expect($sql)->toContain('order by')
        ->and($sql)->toContain('DESC');
});

test('movie query builder can filter by tags', function () {
    $query = Movie::query()->withTags([1, 2, 3]);
    $sql = $query->toSql();

    expect($sql)->toContain('exists')
        ->and($sql)->toContain('tags');
});

test('movie query builder can filter by persons', function () {
    $query = Movie::query()->withPersons([1, 2, 3]);
    $sql = $query->toSql();

    expect($sql)->toContain('exists')
        ->and($sql)->toContain('persons');
});

test('movie query builder can filter by countries', function () {
    $query = Movie::query()->fromCountries(['US', 'UK']);
    $sql = $query->toSql();

    expect($sql)->toContain('countries');
});

test('user query builder can get active users', function () {
    $query = User::query()->active(30);
    $sql = $query->toSql();

    expect($sql)->toContain('"last_seen_at" >= ?');
});

test('user query builder can get inactive users', function () {
    $query = User::query()->inactive(30);
    $sql = $query->toSql();

    expect($sql)->toContain('"last_seen_at" < ?');
});

test('user query builder can get users with active subscriptions', function () {
    $query = User::query()->withActiveSubscriptions();
    $sql = $query->toSql();

    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"is_active" = ?');
});

test('user query builder can get users with specific settings', function () {
    $query = User::query()->withSettings(true, false, true);
    $sql = $query->toSql();

    expect($sql)->toContain('"is_auto_next" = ?')
        ->and($sql)->toContain('"is_auto_play" = ?')
        ->and($sql)->toContain('"is_auto_skip_intro" = ?')
        ->and($query->getBindings())->toContain(true)
        ->and($query->getBindings())->toContain(false)
        ->and($query->getBindings())->toContain(true);

});

test('tag query builder can order by popularity', function () {
    $query = Tag::query()->popular();
    $sql = $query->toSql();

    expect($sql)->toContain('order by')
        ->and($sql)->toContain('desc');
});

test('person query builder can order by movie count', function () {
    $query = Person::query()->orderByMovieCount('desc');
    $sql = $query->toSql();

    expect($sql)->toContain('order by')
        ->and($sql)->toContain('desc');
});

test('studio query builder can order by movie count', function () {
    $query = Studio::query()->orderByMovieCount('desc');
    $sql = $query->toSql();

    expect($sql)->toContain('order by')
        ->and($sql)->toContain('desc');
});
