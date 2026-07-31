<?php

use App\Models\Book;
use App\Models\User;
use App\Models\Activity;
use App\Enums\UserBookStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('API test')->plainTextToken;
});

it('returns the dashboard, library, book detail, and activity data used by the app', function () {
    $book = Book::factory()->create();
    $this->user->books()->attach($book, [
        'status' => UserBookStatus::Reading->value,
        'tags' => ['favourite'],
    ]);
    $activity = Activity::factory()->for($this->user)->create();

    $this->withToken($this->token)->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('stats.books_in_library', 1)
        ->assertJsonPath('stats.reading_books', 1)
        ->assertJsonPath('currently_reading.0.identifier', $book->identifier)
        ->assertJsonPath('activities.0.properties', $activity->properties);

    $this->withToken($this->token)->getJson('/api/user/books?status[]='.urlencode(UserBookStatus::Reading->value))
        ->assertOk()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('filtered_total', 1)
        ->assertJsonPath('books.0.identifier', $book->identifier)
        ->assertJsonPath('books.0.user_tags.0', 'favourite');

    $this->withToken($this->token)->getJson('/api/books/'.$book->identifier)
        ->assertOk()
        ->assertJsonPath('book.identifier', $book->identifier)
        ->assertJsonPath('book.in_library', true)
        ->assertJsonPath('book.user_status', UserBookStatus::Reading->value)
        ->assertJsonStructure(['average_rating', 'related', 'reviews']);

    $this->withToken($this->token)->getJson('/api/user/activities')
        ->assertOk()
        ->assertJsonPath('data.0.id', $activity->id)
        ->assertJsonPath('data.0.properties', $activity->properties);
});

it('manages a library entry through the API', function () {
    $book = Book::factory()->create();

    $this->withToken($this->token)->postJson('/api/user/books', [
        'identifier' => $book->identifier,
        'status' => UserBookStatus::PlanToRead->value,
    ])->assertOk();

    $this->assertDatabaseHas('book_user', [
        'book_id' => $book->id,
        'user_id' => $this->user->id,
        'status' => UserBookStatus::PlanToRead->value,
    ]);

    $this->withToken($this->token)->patchJson('/api/user/'.$book->identifier.'/status', [
        'status' => UserBookStatus::Read->value,
    ])->assertOk()->assertJsonPath('status', UserBookStatus::Read->value);

    $this->withToken($this->token)->putJson('/api/user/'.$book->identifier.'/tags', [
        'tags' => ['classic', 'favourite'],
    ])->assertOk()->assertJsonPath('tags.1', 'favourite');

    expect($this->user->books()->whereKey($book->id)->first()->pivot->tags)
        ->toBe(['classic', 'favourite']);

    $this->withToken($this->token)->deleteJson('/api/user/'.$book->identifier)
        ->assertOk();

    $this->assertDatabaseMissing('book_user', [
        'book_id' => $book->id,
        'user_id' => $this->user->id,
    ]);
});

it('manages private notes, reviews, and ratings through scoped book routes', function () {
    config()->set('subscriptions.plans.free.limits.private_notes', true);

    $book = Book::factory()->create();
    $this->user->books()->attach($book, ['status' => UserBookStatus::Reading->value]);

    $noteId = $this->withToken($this->token)->postJson('/api/books/'.$book->identifier.'/notes', [
        'content' => 'A private note',
    ])->assertCreated()->json('id');

    $reviewUuid = $this->withToken($this->token)->postJson('/api/books/'.$book->identifier.'/reviews', [
        'title' => 'Worth reading',
        'content' => 'A concise review.',
    ])->assertCreated()->json('uuid');

    $ratingId = $this->withToken($this->token)->postJson('/api/books/'.$book->identifier.'/ratings', [
        'rating' => ['value' => 4],
    ])->assertCreated()->json('id');

    $this->withToken($this->token)->getJson('/api/user/notes')
        ->assertOk()
        ->assertJsonPath('data.0.id', $noteId);

    $this->withToken($this->token)->getJson('/api/user/reviews')
        ->assertOk()
        ->assertJsonPath('data.0.uuid', $reviewUuid);

    $this->withToken($this->token)->putJson('/api/books/'.$book->identifier.'/ratings/'.$ratingId, [
        'rating' => ['value' => 5],
    ])->assertOk()->assertJsonPath('value', 5);

    $this->withToken($this->token)->deleteJson('/api/books/'.$book->identifier.'/notes/'.$noteId)
        ->assertNoContent();
    $this->withToken($this->token)->deleteJson('/api/books/'.$book->identifier.'/reviews/'.$reviewUuid)
        ->assertNoContent();
    $this->withToken($this->token)->deleteJson('/api/books/'.$book->identifier.'/ratings/'.$ratingId)
        ->assertNoContent();
});

it('rejects nested resources that do not belong to the route book', function () {
    config()->set('subscriptions.plans.free.limits.private_notes', true);

    [$firstBook, $secondBook] = Book::factory()->count(2)->create();
    $this->user->books()->attach([$firstBook->id, $secondBook->id], ['status' => UserBookStatus::Reading->value]);
    $note = $firstBook->notes()->create([
        'user_id' => $this->user->id,
        'content' => 'Scoped note',
    ]);

    $this->withToken($this->token)
        ->deleteJson('/api/books/'.$secondBook->identifier.'/notes/'.$note->id)
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});

it('uploads and removes a custom cover for a book in the library', function () {
    config()->set('subscriptions.plans.free.limits.custom_covers', true);
    Storage::fake('public');

    $book = Book::factory()->create();
    $this->user->books()->attach($book, ['status' => UserBookStatus::Reading->value]);

    $this->withToken($this->token)
        ->post('/api/books/'.$book->identifier.'/cover', [
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonPath('identifier', $book->identifier);

    $this->assertDatabaseHas('covers', [
        'book_id' => $book->id,
        'user_id' => $this->user->id,
    ]);

    $this->withToken($this->token)->deleteJson('/api/books/'.$book->identifier.'/cover')
        ->assertNoContent();
});

it('updates account data and returns JSON validation and model errors', function () {
    $this->withToken($this->token)->postJson('/api/user/profile', [
        'name' => 'Mobile Reader',
        'username' => 'mobile-reader',
        'email' => $this->user->email,
        'profile_colour' => '#123456',
    ])->assertOk()
        ->assertJsonPath('name', 'Mobile Reader')
        ->assertJsonPath('colour', '#123456');

    $this->withToken($this->token)->putJson('/api/user/password', [
        'current_password' => 'password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertNoContent();

    expect(Hash::check('new-secure-password', $this->user->fresh()->password))->toBeTrue();

    $this->withToken($this->token)->getJson('/api/books/search?per_page=31')
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor('per_page');

    $book = Book::factory()->create();
    $this->withToken($this->token)->postJson('/api/books/resolve', [
        'identifier' => $book->identifier,
    ])->assertOk()->assertJsonPath('book.identifier', $book->identifier);

    $this->withToken($this->token)->getJson('/api/books/missing-identifier')
        ->assertNotFound()
        ->assertJsonStructure(['message']);

    $this->withToken($this->token)->deleteJson('/api/user', [
        'password' => 'new-secure-password',
    ])->assertNoContent();

    $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    expect($this->user->tokens()->count())->toBe(0);
});
