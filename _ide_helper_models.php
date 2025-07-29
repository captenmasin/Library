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
     * @property int $id
     * @property int $user_id
     * @property string|null $subject_type
     * @property int|null $subject_id
     * @property string $type
     * @property array<array-key, mixed>|null $properties
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read string $description
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $subject
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereProperties($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereSubjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereSubjectType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUserId($value)
     */
    class Activity extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $uuid
     * @property string|null $slug
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
     * @property-read int|null $books_count
     *
     * @method static \Database\Factories\AuthorFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Author whereUuid($value)
     */
    class Author extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $title
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $published_date
     * @property string|null $settings
     * @property string|null $identifier
     * @property array<array-key, mixed>|null $codes
     * @property string|null $path
     * @property int|null $page_count
     * @property int|null $publisher_id
     * @property string|null $service
     * @property string|null $original_cover
     * @property string|null $edition
     * @property string|null $binding
     * @property string|null $language
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Author> $authors
     * @property-read int|null $authors_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cover> $covers
     * @property-read int|null $covers_count
     * @property-read string $primary_cover
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
     * @property-read int|null $notes_count
     * @property-read \App\Models\Publisher|null $publisher
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
     * @property-read int|null $ratings_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
     * @property-read int|null $reviews_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
     * @property-read int|null $tags_count
     * @property-read \App\Models\BookUser|null $pivot
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     *
     * @method static \Database\Factories\BookFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereBinding($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCodes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereEdition($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereIdentifier($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereLanguage($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereOriginalCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePageCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePublishedDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePublisherId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereService($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereSettings($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUpdatedAt($value)
     */
    class Book extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $book_id
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string $status
     * @property array<array-key, mixed>|null $tags
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereBookId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereTags($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|BookUser whereUserId($value)
     */
    class BookUser extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $book_id
     * @property int $is_primary
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int|null $user_id
     * @property-read \App\Models\Book $book
     * @property-read string $image
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \App\Models\User|null $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereBookId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereIsPrimary($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Cover whereUserId($value)
     */
    class Cover extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $user_id
     * @property int $book_id
     * @property string|null $book_status
     * @property string|null $content
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Book $Book
     * @property-read \App\Models\User $User
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     *
     * @method static \Database\Factories\NoteFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereBookId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereBookStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUserId($value)
     */
    class Note extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $user_id
     * @property string $search_term
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch whereSearchTerm($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PreviousSearch whereUserId($value)
     */
    class PreviousSearch extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $uuid
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
     * @property-read int|null $books_count
     *
     * @method static \Database\Factories\PublisherFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Publisher whereUuid($value)
     */
    class Publisher extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $book_id
     * @property int $user_id
     * @property int $value
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Database\Factories\RatingFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereBookId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereValue($value)
     */
    class Rating extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $book_id
     * @property int $user_id
     * @property string|null $content
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string $uuid
     * @property string|null $title
     * @property-read \App\Models\Book $book
     * @property-read mixed $rating
     * @property-read \App\Models\User $user
     *
     * @method static \Database\Factories\ReviewFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereBookId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUuid($value)
     */
    class Review extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $slug
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
     * @property-read int|null $books_count
     *
     * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
     */
    class Tag extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $username
     * @property string|null $settings
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
     * @property-read int|null $activities_count
     * @property-read string $avatar
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cover> $book_covers
     * @property-read int|null $book_covers_count
     * @property-read \App\Models\BookUser|null $pivot
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
     * @property-read int|null $books_count
     * @property-read string $has_avatar
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Note> $notes
     * @property-read int|null $notes_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\LaravelPasskeys\Models\Passkey> $passkeys
     * @property-read int|null $passkeys_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PreviousSearch> $previousSearches
     * @property-read int|null $previous_searches_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
     * @property-read int|null $ratings_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
     * @property-read int|null $reviews_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
     * @property-read int|null $tokens_count
     *
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSettings($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
     */
    class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \Illuminate\Contracts\Auth\MustVerifyEmail, \Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys, \Spatie\MediaLibrary\HasMedia {}
}
