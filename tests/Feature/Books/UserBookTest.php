<?php

use App\Models\Book;
use App\Models\User;
use App\Enums\UserBookStatus;
use Inertia\Testing\AssertableInertia;

test('library page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('user.books.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Index')
        ->has('books')
    );
});

test('library page shows user books', function () {
    $user = User::factory()->create();
    $books = Book::factory()->count(3)->create();

    // Add books to user's library
    foreach ($books as $book) {
        $user->books()->attach($book, [
            'status' => UserBookStatus::Completed->value,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('user.books.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Index')
        ->has('books', 3)
    );
});

test('library can be filtered by status', function () {
    $user = User::factory()->create();
    $completedBooks = Book::factory()->count(2)->create();
    $planToReadBooks = Book::factory()->count(3)->create();

    // Add books with different statuses
    foreach ($completedBooks as $book) {
        $user->books()->attach($book, [
            'status' => UserBookStatus::Completed->value,
        ]);
    }

    foreach ($planToReadBooks as $book) {
        $user->books()->attach($book, [
            'status' => UserBookStatus::PlanToRead->value,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('user.books.index', ['status' => UserBookStatus::Completed->value]));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Index')
        ->has('books', 2)
    );
});

test('book can be added to library', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('api.user.books.store'), [
            'identifier' => $book->identifier,
            'status' => UserBookStatus::Reading->value,
        ]);

    $response->assertRedirect();

    // Verify book was added to user's library
    $this->assertDatabaseHas('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => UserBookStatus::Reading->value,
    ]);
});

test('book tags can be updated', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    // Add book to user's library first
    $user->books()->attach($book, [
        'status' => UserBookStatus::Reading->value,
    ]);

    $tags = ['fiction', 'fantasy'];

    $response = $this->actingAs($user)
        ->put(route('user.books.update_tags', $book), [
            'tags' => $tags,
        ]);

    $response->assertRedirect();

    // Verify tags were updated
    $this->assertDatabaseHas('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'tags' => json_encode($tags),
    ]);
});

test('library page requires authentication', function () {
    $response = $this->get(route('user.books.index'));

    $response->assertRedirect(route('login'));
});
