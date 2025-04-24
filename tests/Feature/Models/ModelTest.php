<?php

use App\Enums\Kind;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Builders\MovieQueryBuilder;
use App\Models\Builders\UserQueryBuilder;
use App\Models\Movie;
use App\Models\User;

test('models have correct query builders', function () {
    expect(Movie::query())->toBeInstanceOf(MovieQueryBuilder::class)
        ->and(User::query())->toBeInstanceOf(UserQueryBuilder::class);
});

test('models have correct casts', function () {
    $movie = new Movie();
    $movieCasts = $movie->getCasts();

    expect($movieCasts)->toBeArray()
        ->toHaveKey('kind', Kind::class)
        ->toHaveKey('status', Status::class)
        ->toHaveKey('is_published', 'boolean');

    $user = new User();
    $userCasts = $user->getCasts();

    expect($userCasts)->toBeArray()
        ->toHaveKey('role', Role::class)
        ->toHaveKey('is_banned', 'boolean');
});

test('movie query builder can filter by kind', function () {
    $query = Movie::query()->ofKind(Kind::MOVIE);
    $sql = $query->toSql();

    expect($sql)->toContain('"kind" = ?')
        ->and($query->getBindings())->toContain(Kind::MOVIE->value);
});

test('movie query builder can filter by status', function () {
    $query = Movie::query()->withStatus(Status::RELEASED);
    $sql = $query->toSql();

    expect($sql)->toContain('"status" = ?')
        ->and($query->getBindings())->toContain(Status::RELEASED->value);
});

test('user query builder can filter by role', function () {
    $query = User::query()->byRole(Role::ADMIN);
    $sql = $query->toSql();

    expect($sql)->toContain('"role" = ?')
        ->and($query->getBindings())->toContain(Role::ADMIN->value);
});

test('user query builder can filter banned users', function () {
    $query = User::query()->banned();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_banned" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('user query builder can filter non-banned users', function () {
    $query = User::query()->notBanned();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_banned" = ?')
        ->and($query->getBindings())->toContain(false);
});
