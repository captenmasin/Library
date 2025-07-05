<?php

use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\SearchBooksFromApi;
use App\Contracts\BookApiServiceInterface;
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
            'items' => [
                [
                    'identifier' => '9780747532743',
                    'title' => 'Harry Potter',
                    'authors' => ['J.K. Rowling'],
                    'publishedDate' => '1997-06-26',
                    'description' => 'A young wizard embarks on an adventure.',
                    'pageCount' => 223,
                    'cover' => 'https://example.com/cover.jpg',
                    'codes' => [
                        ['type' => 'ISBN_13', 'identifier' => '9780747532743'],
                        ['type' => 'ISBN_10', 'identifier' => '0747532745'],
                    ],
                ],
            ],
            'total' => 1,
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
    $mock = Mockery::mock(BookApiServiceInterface::class);
    $mock->shouldReceive('get')
        ->with($identifier)
        ->andReturn([
            'isbn' => $identifier,
            'title' => 'Harry Potter',
            'authors' => [
                'J.K. Rowling',
            ],
            'published_date' => '1997-06-26',
            'description' => 'A young wizard embarks on an adventure.',
            'pageCount' => 223,
            'cover' => 'https://example.com/cover.jpg',
            'codes' => [
                ['type' => 'ISBN_13', 'identifier' => '9780747532743'],
                ['type' => 'ISBN_10', 'identifier' => '0747532745'],
            ],
        ]);

    $this->app->instance(BookApiServiceInterface::class, $mock);

    \Illuminate\Support\Facades\Queue::fake();

    $response = $this->get(route('books.preview', ['identifier' => $identifier]));

    ImportBookFromData::assertPushed(1);

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('books/Preview')
        ->where('identifier', $identifier)
    );
});
