<?php

use App\Actions\Books\SearchBooksFromApi;
use App\Models\Book;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('search page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('books.search'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Search')
        ->has('initialQuery')
        ->has('initialAuthor')
        ->has('page')
        ->has('perPage')
    );
});

test('search page requires authentication', function () {
    $response = $this->get(route('books.search'));

    $response->assertRedirect(route('login'));
});

test('book search returns results', function () {
    $user = User::factory()->create();

    // Mock the SearchBooksFromApi action
    $mock = Mockery::mock(SearchBooksFromApi::class);
    $mock->shouldReceive('run')
        ->with('harry potter', null, 10, 1)
        ->andReturn([
            'data' => [
                [
                    'identifier' => '9780747532743',
                    'title' => 'Harry Potter',
                    'authors' => ['J.K. Rowling'],
                ],
            ],
            'meta' => [
                'total' => 1,
                'page' => 1,
                'per_page' => 10,
            ],
        ]);

    $this->app->instance(SearchBooksFromApi::class, $mock);

    $response = $this->actingAs($user)
        ->get(route('books.search', ['q' => 'harry potteer']));

    $response->assertStatus(200);
});

test('book details can be viewed', function () {
    $book = Book::factory()->create();

    $response = $this->get(route('books.show', $book));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Show')
        ->has('book')
        ->has('averageRating')
    );
});

test('book can be updated with notes', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $notes = 'These are my test notes for this book.';

    $response = $this->actingAs($user)
        ->patch(route('books.update', $book), [
            'notes' => $notes,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check that the note was actually saved
    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => $notes,
    ]);
});

test('book preview redirects if book exists', function () {
    $book = Book::factory()->create();

    $response = $this->get(route('books.preview', $book->identifier));

    $response->assertRedirect(route('books.show', $book));
});

test('book preview displays for new book', function () {
    $identifier = '9780747532743';

    $response = $this->get(route('books.preview', $identifier));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Preview')
        ->where('identifier', $identifier)
    );
});
