<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Rating;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserList;
use App\Policies\CommentLikePolicy;
use App\Policies\CommentPolicy;
use App\Policies\CommentReportPolicy;
use App\Policies\EpisodePolicy;
use App\Policies\MoviePolicy;
use App\Policies\PersonPolicy;
use App\Policies\RatingPolicy;
use App\Policies\SelectionPolicy;
use App\Policies\StudioPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserListPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Comment::class => CommentPolicy::class,
        CommentLike::class => CommentLikePolicy::class,
        CommentReport::class => CommentReportPolicy::class,
        Episode::class => EpisodePolicy::class,
        Movie::class => MoviePolicy::class,
        Person::class => PersonPolicy::class,
        Rating::class => RatingPolicy::class,
        Selection::class => SelectionPolicy::class,
        Studio::class => StudioPolicy::class,
        Tag::class => TagPolicy::class,
        User::class => UserPolicy::class,
        UserList::class => UserListPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Model::unguard();
        Model::shouldBeStrict();

        Relation::enforceMorphMap([
            'comment' => Comment::class,
            'commentLike' => CommentLike::class,
            'commentReport' => CommentReport::class,
            'episode' => Episode::class,
            'movie' => Movie::class,
            'person' => Person::class,
            'rating' => Rating::class,
            'selection' => Selection::class,
            'studio' => Studio::class,
            'tag' => Tag::class,
            'user' => User::class,
            'userList' => UserList::class,
        ]);


        Blueprint::macro('enumAlterColumn',
            function (
                string $columnName,
                string $enumTypeName,
                string $enumClass,
                ?string $default = null,
                bool $nullable = false
            ) {
                // Генеруємо список значень enum
                $value = collect($enumClass::cases())
                    ->map(fn($case) => "'{$case->value}'")
                    ->implode(',');

                // Створюємо тип enum, якщо він ще не існує
                DB::statement(sprintf(
                    "DO $$ BEGIN
                                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = '%s') THEN
                                    CREATE TYPE %s AS ENUM (%s);
                                END IF;
                            END $$;",
                    $enumTypeName,
                    $enumTypeName,
                    $value
                ));

                // Додаємо стовпець з типом enum та nullable, якщо це необхідно
                $nullableClause = $nullable ? 'NULL' : 'NOT NULL';

                DB::statement(sprintf(
                    'ALTER TABLE "%s" ADD COLUMN "%s" %s %s;',
                    $this->getTable(),
                    $columnName,
                    $enumTypeName,
                    $nullableClause
                ));

                // Якщо задано значення за замовчуванням, додаємо його
                if ($default) {
                    DB::statement(sprintf(
                        'ALTER TABLE "%s" ALTER COLUMN "%s" SET DEFAULT %s;',
                        $this->getTable(),
                        $columnName,
                        "'{$default}'"
                    ));
                }
            });

    }
}
