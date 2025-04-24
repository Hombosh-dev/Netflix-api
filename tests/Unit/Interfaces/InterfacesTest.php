<?php

use App\Interfaces\Commentable;
use App\Interfaces\Listable;
use App\Interfaces\Selectionable;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Tag;

test('movie implements correct interfaces', function () {
    $movie = new Movie();

    expect($movie)->toBeInstanceOf(Listable::class)
        ->and($movie)->toBeInstanceOf(Commentable::class)
        ->and($movie)->toBeInstanceOf(Selectionable::class);
});

test('episode implements correct interfaces', function () {
    $episode = new Episode();

    expect($episode)->toBeInstanceOf(Listable::class)
        ->and($episode)->toBeInstanceOf(Commentable::class);
});

test('person implements correct interfaces', function () {
    $person = new Person();

    expect($person)->toBeInstanceOf(Listable::class)
        ->and($person)->toBeInstanceOf(Selectionable::class);
});

test('tag implements correct interfaces', function () {
    $tag = new Tag();

    expect($tag)->toBeInstanceOf(Listable::class);
});

test('commentable interface requires comments method', function () {
    $reflection = new ReflectionClass(Commentable::class);
    $methods = $reflection->getMethods();

    expect($methods)->toHaveCount(1)
        ->and($methods[0]->getName())->toBe('comments');
});

test('listable interface requires userLists method', function () {
    $reflection = new ReflectionClass(Listable::class);
    $methods = $reflection->getMethods();

    expect($methods)->toHaveCount(1)
        ->and($methods[0]->getName())->toBe('userLists');
});

test('selectionable interface requires selections method', function () {
    $reflection = new ReflectionClass(Selectionable::class);
    $methods = $reflection->getMethods();

    expect($methods)->toHaveCount(1)
        ->and($methods[0]->getName())->toBe('selections');
});
