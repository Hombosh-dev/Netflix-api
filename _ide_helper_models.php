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
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read string $translated_type
 * @property-read mixed $is_reply
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $likes
 * @property-read int|null $likes_count
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment replies()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsSpoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
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
 * @property string $comment_id
 * @property string $user_id
 * @property bool $is_liked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike byComment(string $commentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike byUser(string $userId)
 * @method static \Database\Factories\CommentLikeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike onlyDislikes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike onlyLikes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereIsLiked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereUserId($value)
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
 * @property bool $is_viewed
 * @property string|null $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\CommentReportType $type
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport byComment(string $commentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport byUser(string $userId)
 * @method static \Database\Factories\CommentReportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport unViewed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereIsViewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReport whereUserId($value)
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
 * @property \Illuminate\Support\Carbon|null $air_date
 * @property bool $is_filler
 * @property \Illuminate\Support\Collection $pictures
 * @property array<array-key, mixed> $video_players
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Movie $movie
 * @property-read mixed $picture_url
 * @property-read mixed $pictures_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode airedAfter(\Carbon\Carbon $date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode bySlug(string $slug)
 * @method static \Database\Factories\EpisodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode forMovie(string $movieId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereIsFiller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode wherePictures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereVideoPlayers($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEpisode {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property array<array-key, mixed> $api_sources
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $image_name
 * @property \Illuminate\Support\Collection $aliases
 * @property string $studio_id
 * @property array<array-key, mixed> $countries
 * @property string|null $poster
 * @property int|null $duration
 * @property-read int|null $episodes_count
 * @property \Illuminate\Support\Carbon|null $first_air_date
 * @property \Illuminate\Support\Carbon|null $last_air_date
 * @property float|null $imdb_score
 * @property array<array-key, mixed> $attachments
 * @property array<array-key, mixed> $related
 * @property \Illuminate\Support\Collection $similars
 * @property bool $is_published
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\Kind $kind
 * @property \App\Enums\Status $status
 * @property string|null $searchable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Episode> $episodes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $persons
 * @property-read int|null $persons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \App\Models\Studio $studio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie bySlug(string $slug)
 * @method static \Database\Factories\MovieFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie ofKind(\App\Enums\Kind $kind)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie search(string $search)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSimilars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie withImdbScoreGreaterThan(float $score)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie withStatus(\App\Enums\Status $status)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovie {}
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
 * @property \App\Enums\PersonType $type
 * @property \App\Enums\Gender|null $gender
 * @property string|null $searchable
 * @property-read mixed $age
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person byGender(string $gender)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person byName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person bySlug(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person byType(\App\Enums\PersonType $type)
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthplace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPerson {}
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating betweenRatings(int $minRating, int $maxRating)
 * @method static \Database\Factories\RatingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating forMovie(string $movieId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating forUser(string $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRating {}
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
 * @property string|null $searchable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $persons
 * @property-read int|null $persons_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection bySlug(string $slug)
 * @method static \Database\Factories\SelectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Selection whereUserId($value)
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
 * @property string|null $searchable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio byName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio bySlug(string $slug)
 * @method static \Database\Factories\StudioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereIsGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereUpdatedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag bySlug(string $slug)
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag genres()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
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
 * @property \App\Enums\Role $role
 * @property \App\Enums\Gender|null $gender
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $commentLikes
 * @property-read int|null $comment_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentReport> $commentReports
 * @property-read int|null $comment_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $favoriteEpisodes
 * @property-read int|null $favorite_episodes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $favoriteMovies
 * @property-read int|null $favorite_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $favoritePeople
 * @property-read int|null $favorite_people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $favoriteTags
 * @property-read int|null $favorite_tags_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $plannedMovies
 * @property-read int|null $planned_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $reWatchingMovies
 * @property-read int|null $re_watching_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $stoppedMovies
 * @property-read int|null $stopped_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $watchedMovies
 * @property-read int|null $watched_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $watchingMovies
 * @property-read int|null $watching_movies_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User allowedAdults()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole(\App\Enums\Role $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User vipCustomer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAllowAdult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBackdrop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAutoNext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAutoPlay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAutoSkipIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsPrivateFavorites($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
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
 * @property \App\Enums\UserListType $type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $listable
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\UserListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList forUser(string $userId, ?string $listableClass = null, ?\App\Enums\UserListType $userListType = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList ofType(\App\Enums\UserListType $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereListableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereListableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserList {}
}

