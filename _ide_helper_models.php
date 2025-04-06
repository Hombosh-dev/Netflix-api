<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $commentable_type
 * @property string $commentable_id
 * @property string $user_id
 * @property bool $is_spoiler
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $children
 * @property-read int|null $children_count
 * @property-read Model|\Eloquent $commentable
 * @property-read mixed $is_reply
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $likes
 * @property-read int|null $likes_count
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Comment newModelQuery()
 * @method static Builder<static>|Comment newQuery()
 * @method static Builder<static>|Comment query()
 * @method static Builder<static>|Comment replies()
 * @method static Builder<static>|Comment roots()
 * @method static Builder<static>|Comment whereBody($value)
 * @method static Builder<static>|Comment whereCommentableId($value)
 * @method static Builder<static>|Comment whereCommentableType($value)
 * @method static Builder<static>|Comment whereCreatedAt($value)
 * @method static Builder<static>|Comment whereId($value)
 * @method static Builder<static>|Comment whereIsSpoiler($value)
 * @method static Builder<static>|Comment whereParentId($value)
 * @method static Builder<static>|Comment whereUpdatedAt($value)
 * @method static Builder<static>|Comment whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperComment {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment|null $comment
 * @property-read \App\Models\User|null $user
 * @method static Builder<static>|CommentLike byComment(string $commentId)
 * @method static Builder<static>|CommentLike byUser(string $userId)
 * @method static \Database\Factories\CommentLikeFactory factory($count = null, $state = [])
 * @method static Builder<static>|CommentLike newModelQuery()
 * @method static Builder<static>|CommentLike newQuery()
 * @method static Builder<static>|CommentLike onlyDislikes()
 * @method static Builder<static>|CommentLike onlyLikes()
 * @method static Builder<static>|CommentLike query()
 * @method static Builder<static>|CommentLike whereCreatedAt($value)
 * @method static Builder<static>|CommentLike whereId($value)
 * @method static Builder<static>|CommentLike whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCommentLike {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $comment_id
 * @property string $user_id
 * @property bool $is_liked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property CommentReportType $type
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static Builder<static>|CommentReport byComment(string $commentId)
 * @method static Builder<static>|CommentReport byUser(string $userId)
 * @method static \Database\Factories\CommentReportFactory factory($count = null, $state = [])
 * @method static Builder<static>|CommentReport newModelQuery()
 * @method static Builder<static>|CommentReport newQuery()
 * @method static Builder<static>|CommentReport query()
 * @method static Builder<static>|CommentReport unViewed()
 * @method static Builder<static>|CommentReport whereCommentId($value)
 * @method static Builder<static>|CommentReport whereCreatedAt($value)
 * @method static Builder<static>|CommentReport whereId($value)
 * @method static Builder<static>|CommentReport whereIsLiked($value)
 * @method static Builder<static>|CommentReport whereUpdatedAt($value)
 * @method static Builder<static>|CommentReport whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCommentReport {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $movie_id
 * @property int $number
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property int|null $duration
 * @property string|null $air_date
 * @property bool $is_filler
 * @property string $pictures
 * @property string $video_players
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read mixed $picture_url
 * @property-read mixed $pictures_url
 * @method static Builder<static>|Episode airedAfter(\Carbon\Carbon $date)
 * @method static \Database\Factories\EpisodeFactory factory($count = null, $state = [])
 * @method static Builder<static>|Episode forMovie(string $movieId)
 * @method static Builder<static>|Episode newModelQuery()
 * @method static Builder<static>|Episode newQuery()
 * @method static Builder<static>|Episode query()
 * @method static Builder<static>|Episode whereAirDate($value)
 * @method static Builder<static>|Episode whereCreatedAt($value)
 * @method static Builder<static>|Episode whereDescription($value)
 * @method static Builder<static>|Episode whereDuration($value)
 * @method static Builder<static>|Episode whereId($value)
 * @method static Builder<static>|Episode whereIsFiller($value)
 * @method static Builder<static>|Episode whereMetaDescription($value)
 * @method static Builder<static>|Episode whereMetaImage($value)
 * @method static Builder<static>|Episode whereMetaTitle($value)
 * @method static Builder<static>|Episode whereMovieId($value)
 * @method static Builder<static>|Episode whereName($value)
 * @method static Builder<static>|Episode whereNumber($value)
 * @method static Builder<static>|Episode wherePictures($value)
 * @method static Builder<static>|Episode whereSlug($value)
 * @method static Builder<static>|Episode whereUpdatedAt($value)
 * @method static Builder<static>|Episode whereVideoPlayers($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEpisode {}
}

namespace App\Models{
/**
 *
 *
 * @property int $id
 * @property string $api_sources
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $image_name
 * @property string $aliases
 * @property string $studio_id
 * @property string $countries
 * @property string|null $poster
 * @property int|null $duration
 * @property int|null $episodes_count
 * @property string|null $first_air_date
 * @property string|null $last_air_date
 * @property string|null $imdb_score
 * @property string $attachments
 * @property string $related
 * @property string $similars
 * @property bool $is_published
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $kind
 * @property-read \App\Models\Studio $studio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereApiSources($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereEpisodesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereFirstAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereImdbScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereLastAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereRelated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSimilars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovie {}
}

namespace App\Models{
/**
 *
 *
 * @property string $user_id
 * @property string $movie_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MovieNotificationsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovieNotifications {}
}

namespace App\Models{
/**
 *
 *
 * @property string $movie_id
 * @property string $tag_id
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\Tags $tag
 * @method static \Database\Factories\MovieTagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag whereTagId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovieTag {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string|null $original_name
 * @property string|null $image
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property string|null $birthplace
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property PersonType $type
 * @property Gender|null $gender
 * @property-read mixed $age
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @method static Builder<static>|People byGender(string $gender)
 * @method static Builder<static>|People byName(string $name)
 * @method static Builder<static>|People byType(\App\Enums\PersonType $type)
 * @method static \Database\Factories\PeopleFactory factory($count = null, $state = [])
 * @method static Builder<static>|People newModelQuery()
 * @method static Builder<static>|People newQuery()
 * @method static Builder<static>|People query()
 * @method static Builder<static>|People search(string $search)
 * @method static Builder<static>|People whereBirthday($value)
 * @method static Builder<static>|People whereBirthplace($value)
 * @method static Builder<static>|People whereCreatedAt($value)
 * @method static Builder<static>|People whereDescription($value)
 * @method static Builder<static>|People whereGender($value)
 * @method static Builder<static>|People whereId($value)
 * @method static Builder<static>|People whereImage($value)
 * @method static Builder<static>|People whereMetaDescription($value)
 * @method static Builder<static>|People whereMetaImage($value)
 * @method static Builder<static>|People whereMetaTitle($value)
 * @method static Builder<static>|People whereName($value)
 * @method static Builder<static>|People whereOriginalName($value)
 * @method static Builder<static>|People whereSlug($value)
 * @method static Builder<static>|People whereType($value)
 * @method static Builder<static>|People whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPeople {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string $movie_id
 * @property int $number
 * @property mixed|null $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Ratings betweenRatings(int $minRating, int $maxRating)
 * @method static \Database\Factories\RatingsFactory factory($count = null, $state = [])
 * @method static Builder<static>|Ratings forMovie(string $movieId)
 * @method static Builder<static>|Ratings forUser(string $userId)
 * @method static Builder<static>|Ratings newModelQuery()
 * @method static Builder<static>|Ratings newQuery()
 * @method static Builder<static>|Ratings query()
 * @method static Builder<static>|Ratings whereCreatedAt($value)
 * @method static Builder<static>|Ratings whereId($value)
 * @method static Builder<static>|Ratings whereMovieId($value)
 * @method static Builder<static>|Ratings whereNumber($value)
 * @method static Builder<static>|Ratings whereReview($value)
 * @method static Builder<static>|Ratings whereUpdatedAt($value)
 * @method static Builder<static>|Ratings whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRatings {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $persons
 * @property-read int|null $persons_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Database\Factories\SelectionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Selection newModelQuery()
 * @method static Builder<static>|Selection newQuery()
 * @method static Builder<static>|Selection query()
 * @method static Builder<static>|Selection search(string $search)
 * @method static Builder<static>|Selection whereCreatedAt($value)
 * @method static Builder<static>|Selection whereDescription($value)
 * @method static Builder<static>|Selection whereId($value)
 * @method static Builder<static>|Selection whereMetaDescription($value)
 * @method static Builder<static>|Selection whereMetaImage($value)
 * @method static Builder<static>|Selection whereMetaTitle($value)
 * @method static Builder<static>|Selection whereName($value)
 * @method static Builder<static>|Selection whereSlug($value)
 * @method static Builder<static>|Selection whereUpdatedAt($value)
 * @method static Builder<static>|Selection whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSelection {}
}

namespace App\Models{
/**
 *
 *
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property string|null $aliases
 * @property bool $is_genre
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @method static Builder<static>|Studio byName(string $name)
 * @method static \Database\Factories\StudioFactory factory($count = null, $state = [])
 * @method static Builder<static>|Studio newModelQuery()
 * @method static Builder<static>|Studio newQuery()
 * @method static Builder<static>|Studio query()
 * @method static Builder<static>|Studio search(string $search)
 * @method static Builder<static>|Studio whereAliases($value)
 * @method static Builder<static>|Studio whereCreatedAt($value)
 * @method static Builder<static>|Studio whereDescription($value)
 * @method static Builder<static>|Studio whereId($value)
 * @method static Builder<static>|Studio whereImage($value)
 * @method static Builder<static>|Studio whereIsGenre($value)
 * @method static Builder<static>|Studio whereMetaDescription($value)
 * @method static Builder<static>|Studio whereMetaImage($value)
 * @method static Builder<static>|Studio whereMetaTitle($value)
 * @method static Builder<static>|Studio whereName($value)
 * @method static Builder<static>|Studio whereSlug($value)
 * @method static Builder<static>|Studio whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudio {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property \Illuminate\Support\Collection $aliases
 * @property bool $is_genre
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Database\Factories\TagsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags genres()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereIsGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTags {}
}

namespace App\Models{
/**
 *
 *
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Role $role
 * @property Gender|null $gender
 * @property string $id
 * @property string|null $avatar
 * @property string|null $backdrop
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property bool $allow_adult
 * @property string|null $last_seen_at
 * @property bool $is_auto_next
 * @property bool $is_auto_play
 * @property bool $is_auto_skip_intro
 * @property bool $is_private_favorites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommentLike> $commentLikes
 * @property-read int|null $comment_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommentReport> $commentReports
 * @property-read int|null $comment_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movieNotifications
 * @property-read int|null $movie_notifications_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @method static Builder<static>|User allowedAdults()
 * @method static Builder<static>|User byRole(\App\Enums\Role $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User isAdmin()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User vipCustomer()
 * @method static Builder<static>|User whereAllowAdult($value)
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereBackdrop($value)
 * @method static Builder<static>|User whereBirthday($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDescription($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereGender($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsAutoNext($value)
 * @method static Builder<static>|User whereIsAutoPlay($value)
 * @method static Builder<static>|User whereIsAutoSkipIntro($value)
 * @method static Builder<static>|User whereIsPrivateFavorites($value)
 * @method static Builder<static>|User whereLastSeenAt($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string $listable_type
 * @property string $listable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property UserListType $type
 * @property-read Model|\Eloquent $listable
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\UserListsFactory factory($count = null, $state = [])
 * @method static Builder<static>|UserList forUser(string $userId, ?string $listableClass = null, ?\App\Enums\UserListType $userListType = null)
 * @method static Builder<static>|UserList newModelQuery()
 * @method static Builder<static>|UserList newQuery()
 * @method static Builder<static>|UserList ofType(\App\Enums\UserListType $type)
 * @method static Builder<static>|UserList query()
 * @method static Builder<static>|UserList whereCreatedAt($value)
 * @method static Builder<static>|UserList whereId($value)
 * @method static Builder<static>|UserList whereListableId($value)
 * @method static Builder<static>|UserList whereListableType($value)
 * @method static Builder<static>|UserList whereType($value)
 * @method static Builder<static>|UserList whereUpdatedAt($value)
 * @method static Builder<static>|UserList whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserLists {}
}

