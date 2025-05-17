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
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment forCommentable(string $commentableType, string $commentableId)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment forUser(string $userId)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment mostLiked(int $limit = 10)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment newModelQuery()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment newQuery()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment query()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment replies()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment roots()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereBody($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereCommentableId($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereCommentableType($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereCreatedAt($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereId($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereIsSpoiler($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereParentId($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereUpdatedAt($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment whereUserId($value)
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment withSpoilers()
 * @method static \App\Models\Builders\CommentQueryBuilder<static>|Comment withoutSpoilers()
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
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike byComment(string $commentId)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike byUser(string $userId)
 * @method static \Database\Factories\CommentLikeFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike newModelQuery()
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike newQuery()
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike onlyDislikes()
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike onlyLikes()
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike query()
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereCommentId($value)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereCreatedAt($value)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereId($value)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereIsLiked($value)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereUpdatedAt($value)
 * @method static \App\Models\Builders\CommentLikeQueryBuilder<static>|CommentLike whereUserId($value)
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
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport byComment(string $commentId)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport byType(\App\Enums\CommentReportType $type)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport byUser(string $userId)
 * @method static \Database\Factories\CommentReportFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport newModelQuery()
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport newQuery()
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport query()
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport unViewed()
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport viewed()
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereBody($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereCommentId($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereCreatedAt($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereId($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereIsViewed($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereType($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereUpdatedAt($value)
 * @method static \App\Models\Builders\CommentReportQueryBuilder<static>|CommentReport whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCommentReport {}
}

namespace App\Models{
/**
 * Episode model representing TV series episodes.
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
 * @property \Illuminate\Support\Collection $video_players
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read mixed $formatted_duration
 * @property-read mixed $full_name
 * @property-read mixed $meta_image_url
 * @property-read \App\Models\Movie $movie
 * @property-read mixed $picture_url
 * @property-read mixed $pictures_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode airedAfter(\Carbon\Carbon $date)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode bySlug(string $slug)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode chaperone()
 * @method static \Database\Factories\EpisodeFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode fillers(bool $includeFiller = false)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode forMovie(string $movieId)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode newModelQuery()
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode newQuery()
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode orderByNumber(string $direction = 'asc')
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode query()
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode recentlyAired(int $days = 7)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereAirDate($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereCreatedAt($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereDescription($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereDuration($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereId($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereIsFiller($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereMetaDescription($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereMetaImage($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereMetaTitle($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereMovieId($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereName($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereNumber($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode wherePictures($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereSlug($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereUpdatedAt($value)
 * @method static \App\Models\Builders\EpisodeQueryBuilder<static>|Episode whereVideoPlayers($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEpisode {}
}

namespace App\Models{
/**
 * Movie model representing films, TV series, and other video content.
 *
 * @property string $id
 * @property \Illuminate\Support\Collection $api_sources
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $image_name
 * @property \Illuminate\Support\Collection $aliases
 * @property string $studio_id
 * @property \Illuminate\Support\Collection $countries
 * @property string|null $poster
 * @property int|null $duration
 * @property-read int|null $episodes_count
 * @property \Illuminate\Support\Carbon|null $first_air_date
 * @property \Illuminate\Support\Carbon|null $last_air_date
 * @property float|null $imdb_score
 * @property \Illuminate\Support\Collection $attachments
 * @property \Illuminate\Support\Collection $related
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
 * @property-read mixed $average_rating
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Episode> $episodes
 * @property-read mixed $formatted_duration
 * @property-read mixed $full_title
 * @property-read mixed $image_url
 * @property-read mixed $is_series
 * @property-read mixed $main_country
 * @property-read mixed $main_genre
 * @property-read mixed $meta_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $persons
 * @property-read int|null $persons_count
 * @property-read mixed $poster_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read mixed $release_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \App\Models\Studio $studio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie bySlug(string $slug)
 * @method static \Database\Factories\MovieFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie fromCountries(array $countryCodes)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie newModelQuery()
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie newQuery()
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie ofKind(\App\Enums\Kind $kind)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie popular()
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie query()
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie recentlyAdded(int $limit = 10)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie releasedInYear(int $year)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie search(string $search, array $fields = [], float $trigramThreshold = '0.5')
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie trending(int $days = 7)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereAliases($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereApiSources($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereAttachments($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereCountries($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereCreatedAt($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereDescription($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereDuration($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereEpisodesCount($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereFirstAirDate($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereId($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereImageName($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereImdbScore($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereIsPublished($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereKind($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereLastAirDate($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereMetaDescription($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereMetaImage($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereMetaTitle($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereName($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie wherePoster($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereRelated($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereSearchable($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereSimilars($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereSlug($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereStatus($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereStudioId($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie whereUpdatedAt($value)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie withAverageRating()
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie withImdbScoreGreaterThan(float $score)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie withPersons(array $personIds)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie withStatus(\App\Enums\Status $status)
 * @method static \App\Models\Builders\MovieQueryBuilder<static>|Movie withTags(array $tagIds)
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
 * @property string $user_id
 * @property string $tariff_id
 * @property numeric $amount
 * @property string $currency
 * @property string $payment_method
 * @property string $transaction_id
 * @property \Illuminate\Support\Collection $liqpay_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\PaymentStatus $status
 * @property-read \App\Models\Tariff $tariff
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment failed()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment forSubscription(string $subscriptionId)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment forUser(string $userId)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment inDateRange(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment newModelQuery()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment newQuery()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment pending()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment query()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment successful()
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereAmount($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereCreatedAt($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereCurrency($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereId($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereLiqpayData($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment wherePaymentMethod($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereStatus($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereTariffId($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereTransactionId($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereUpdatedAt($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment whereUserId($value)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment withAmountGreaterThan(float $amount)
 * @method static \App\Models\Builders\PaymentQueryBuilder<static>|Payment withStatus(\App\Enums\PaymentStatus $status)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPayment {}
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
 * @property-read mixed $image_url
 * @property-read mixed $meta_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person actors()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person byGender(\App\Enums\Gender|string $gender)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person byName(string $name)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person bySlug(string $slug)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person byType(\App\Enums\PersonType $type)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person directors()
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person newModelQuery()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person newQuery()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person orderByMovieCount(string $direction = 'desc')
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person query()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person search(string $search, array $fields = [], float $trigramThreshold = '0.5')
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereBirthday($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereBirthplace($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereCreatedAt($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereDescription($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereGender($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereId($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereImage($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereMetaDescription($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereMetaImage($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereMetaTitle($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereName($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereOriginalName($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereSearchable($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereSlug($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereType($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person whereUpdatedAt($value)
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person withMovieCount()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person withMovies()
 * @method static \App\Models\Builders\PersonQueryBuilder<static>|Person writers()
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
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating betweenRatings(int $minRating, int $maxRating)
 * @method static \Database\Factories\RatingFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating forMovie(string $movieId)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating forUser(string $userId)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating highRatings(int $threshold = 8)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating lowRatings(int $threshold = 4)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating newModelQuery()
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating newQuery()
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating query()
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereCreatedAt($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereId($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereMovieId($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereNumber($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereReview($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereUpdatedAt($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating whereUserId($value)
 * @method static \App\Models\Builders\RatingQueryBuilder<static>|Rating withReviews()
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
 * @property bool $is_published
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
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection bySlug(string $slug)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection byUser(string $userId)
 * @method static \Database\Factories\SelectionFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection newModelQuery()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection newQuery()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection orderByMovieCount(string $direction = 'desc')
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection published()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection query()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection search(string $search, array $fields = [], float $trigramThreshold = '0.5')
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection unpublished()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereCreatedAt($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereDescription($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereId($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereIsPublished($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereMetaDescription($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereMetaImage($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereMetaTitle($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereName($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereSearchable($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereSlug($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereUpdatedAt($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection whereUserId($value)
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection withComments()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection withMovieCount()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection withMovies()
 * @method static \App\Models\Builders\SelectionQueryBuilder<static>|Selection withPersons()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSelection {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property \Illuminate\Support\Collection|null $aliases
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $searchable
 * @property \Illuminate\Support\Collection|null $api_sources
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio byName(string $name)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio bySlug(string $slug)
 * @method static \Database\Factories\StudioFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio newModelQuery()
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio newQuery()
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio orderByMovieCount(string $direction = 'desc')
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio query()
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio search(string $search, array $fields = [], float $trigramThreshold = '0.5')
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereAliases($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereApiSources($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereCreatedAt($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereDescription($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereId($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereImage($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereMetaDescription($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereMetaImage($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereMetaTitle($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereName($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereSearchable($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereSlug($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio whereUpdatedAt($value)
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio withMovieCount()
 * @method static \App\Models\Builders\StudioQueryBuilder<static>|Studio withMovies()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStudio {}
}

namespace App\Models{
/**
 * Tag model representing movie genres and tags.
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
 * @property string|null $searchable
 * @property-read mixed $image_url
 * @property-read mixed $meta_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag bySlug(string $slug)
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag genres()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag newModelQuery()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag newQuery()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag nonGenres()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag popular()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag query()
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag search(string $term)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereAliases($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereCreatedAt($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereDescription($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereId($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereImage($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereIsGenre($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereMetaDescription($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereMetaImage($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereMetaTitle($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereName($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereSearchable($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereSlug($value)
 * @method static \App\Models\Builders\TagQueryBuilder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property numeric $price
 * @property string $currency
 * @property int $duration_days
 * @property \Illuminate\Support\Collection $features
 * @property bool $is_active
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $userSubscriptions
 * @property-read int|null $user_subscriptions_count
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff active()
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff bySlug(string $slug)
 * @method static \Database\Factories\TariffFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff inactive()
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff newModelQuery()
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff newQuery()
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff orderByDuration(string $direction = 'asc')
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff orderByPrice(string $direction = 'asc')
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff query()
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereCreatedAt($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereCurrency($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereDescription($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereDurationDays($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereFeatures($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereId($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereIsActive($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereMetaDescription($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereMetaImage($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereMetaTitle($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereName($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff wherePrice($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereSlug($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff whereUpdatedAt($value)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff withDurationMonths(int $months)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff withPriceBetween(float $minPrice, float $maxPrice)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff withPriceGreaterThan(float $price)
 * @method static \App\Models\Builders\TariffQueryBuilder<static>|Tariff withPriceLessThan(float $price)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTariff {}
}

namespace App\Models{
/**
 * User model representing application users.
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
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property bool $is_auto_next
 * @property bool $is_auto_play
 * @property bool $is_auto_skip_intro
 * @property bool $is_banned
 * @property bool $is_private_favorites
 * @property-read mixed $age
 * @property-read mixed $avatar_url
 * @property-read mixed $backdrop_url
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
 * @property-read mixed $formatted_last_seen
 * @property-read mixed $is_online
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $watchedMovies
 * @property-read int|null $watched_movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $watchingMovies
 * @property-read int|null $watching_movies_count
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User active(int $days = 30)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User admins()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User allowedAdults()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User banned()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User byAgeRange(int $minAge, int $maxAge)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User byRole(\App\Enums\Role $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User inactive(int $days = 30)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User moderators()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User newModelQuery()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User newQuery()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User notBanned()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User query()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereAllowAdult($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereAvatar($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereBackdrop($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereBirthday($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereCreatedAt($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereDescription($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereEmail($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereEmailVerifiedAt($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereGender($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereId($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereIsAutoNext($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereIsAutoPlay($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereIsAutoSkipIntro($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereIsBanned($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereIsPrivateFavorites($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereLastSeenAt($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereName($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User wherePassword($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereRememberToken($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereRole($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User whereUpdatedAt($value)
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User withActiveSubscriptions()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User withAutoRenewableSubscriptions()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User withExpiredSubscriptions()
 * @method static \App\Models\Builders\UserQueryBuilder<static>|User withSettings(?bool $autoNext = null, ?bool $autoPlay = null, ?bool $autoSkipIntro = null)
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
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList excludeTypes(array $types)
 * @method static \Database\Factories\UserListFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList favorites()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList forListable(string $listableType, string $listableId)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList forListableType(string $listableType)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList forUser(string $userId, ?string $listableClass = null, ?\App\Enums\UserListType $userListType = null)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList newModelQuery()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList newQuery()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList ofType(\App\Enums\UserListType $type)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList planned()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList query()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList rewatching()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList stopped()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList watched()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList watching()
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereCreatedAt($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereId($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereListableId($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereListableType($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereType($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereUpdatedAt($value)
 * @method static \App\Models\Builders\UserListQueryBuilder<static>|UserList whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserList {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $user_id
 * @property string $tariff_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property bool $is_active
 * @property bool $auto_renew
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tariff $tariff
 * @property-read \App\Models\User $user
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription active()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription autoRenewable()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription expired()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription expiringSoon(int $days = 7)
 * @method static \Database\Factories\UserSubscriptionFactory factory($count = null, $state = [])
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription forTariff(string $tariffId)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription forUser(string $userId)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription inactive()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription newModelQuery()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription newQuery()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription nonAutoRenewable()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription query()
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereAutoRenew($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereCreatedAt($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereEndDate($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereId($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereIsActive($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereStartDate($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereTariffId($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereUpdatedAt($value)
 * @method static \App\Models\Builders\UserSubscriptionQueryBuilder<static>|UserSubscription whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserSubscription {}
}

