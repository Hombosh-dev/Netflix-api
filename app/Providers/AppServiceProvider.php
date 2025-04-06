<?php

namespace App\Providers;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\MovieNotifications;
use App\Models\MovieTag;
use App\Models\People;
use App\Models\Person;
use App\Models\Ratings;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tags;
use App\Models\User;
use App\Models\UserList;
use App\Policies\CommentPolicy;
use App\Policies\CommentLikePolicy;
use App\Policies\CommentReportPolicy;
use App\Policies\EpisodePolicy;
use App\Policies\MoviePolicy;
use App\Policies\MovieNotificationsPolicy;
use App\Policies\MovieTagPolicy;
use App\Policies\PeoplePolicy;
use App\Policies\PersonPolicy;
use App\Policies\RatingsPolicy;
use App\Policies\SelectionPolicy;
use App\Policies\StudioPolicy;
use App\Policies\TagsPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserListPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Comment::class            => CommentPolicy::class,
        CommentLike::class        => CommentLikePolicy::class,
        CommentReport::class      => CommentReportPolicy::class,
        Episode::class            => EpisodePolicy::class,
        Movie::class              => MoviePolicy::class,
        MovieNotifications::class => MovieNotificationsPolicy::class,
        MovieTag::class           => MovieTagPolicy::class,
        People::class             => PeoplePolicy::class,
        Person::class             => PersonPolicy::class,
        Ratings::class            => RatingsPolicy::class,
        Selection::class          => SelectionPolicy::class,
        Studio::class             => StudioPolicy::class,
        Tags::class               => TagsPolicy::class,
        User::class               => UserPolicy::class,
        UserList::class           => UserListPolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Model::unguard();
        Blueprint::macro('enumAlterColumn',
            function (string $columnName,
                      string $enumTypeName,
                      string $enumClass,
                      ?string $default = null,
                      bool $nullable = false) {
                // Генеруємо список значень enum
                $value = collect($enumClass::cases())
                    ->map(fn ($case) => "'{$case->value}'")
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
