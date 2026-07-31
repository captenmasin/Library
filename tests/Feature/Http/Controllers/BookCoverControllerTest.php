<?php

use App\Models\Book;
use App\Models\User;
use App\Enums\UserBookStatus;

use function Pest\Laravel\post;
use function Pest\Laravel\delete;

use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;

use Tests\Concerns\GiveSubscription;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(GiveSubscription::class);

describe('BookCoverController', function () {
    beforeEach(function () {
        Storage::fake('public');
    });

    it('prevents non-subscribed users from uploading a book cover', function () {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

        actingAs($user);

        $file = UploadedFile::fake()->image('cover.jpg');

        $response = post(route('cover.update', $book), [
            'cover' => $file,
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHas('error', 'Your current plan does not allow adding custom covers.');
        assertDatabaseMissing('covers', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    });

    it('allows subscribed users to upload a book cover', function () {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

        $this->giveActiveSubscription($user, config('subscriptions.plans.pro.key'));

        actingAs($user);

        $file = UploadedFile::fake()->image('cover.jpg');

        $response = post(route('cover.update', $book), [
            'cover' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        assertDatabaseHas('covers', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    });

    it('allows authenticated users to remove a book cover', function () {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);
        actingAs($user);
        $cover = $book->covers()->create(['user_id' => $user->id]);

        $response = delete(route('cover.destroy', $book));

        $response->assertOk();
        assertDatabaseMissing('covers', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    });

    it('prevents users from removing covers for books outside their library', function () {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $cover = $book->covers()->create(['user_id' => $user->id]);

        actingAs($user)
            ->delete(route('cover.destroy', $book))
            ->assertForbidden();

        assertDatabaseHas('covers', ['id' => $cover->id]);
    });

    it('redirects guests attempting to update a book cover', function () {
        $book = Book::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg');

        $response = post(route('cover.update', $book), [
            'cover' => $file,
        ]);

        $response->assertRedirect(route('login'));
    });

    it('redirects guests attempting to remove a book cover', function () {
        $book = Book::factory()->create();
        $response = delete(route('cover.destroy', $book));
        $response->assertRedirect(route('login'));
    });

    it('rejects invalid file types for book covers', function () {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

        $this->giveActiveSubscription($user, config('subscriptions.plans.pro.key'));

        actingAs($user);
        $file = UploadedFile::fake()->create('cover.pdf', 100, 'application/pdf');

        $response = post(route('cover.update', $book), [
            'cover' => $file,
        ]);

        $response->assertSessionHasErrors('cover');
    });
});
