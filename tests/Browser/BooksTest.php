<?php

use App\Models\Book;
use App\Models\User;
use Laravel\Dusk\Browser;
use App\Actions\Books\SearchBooksFromApi;

// Guest should be able to view a single book page

test('guest can view book details', function () {
    $book = Book::factory()->create(['title' => 'Guest Accessible Book']);

    $this->browse(function (Browser $browser) use ($book) {
        $browser->visit(route('books.show', $book))
            ->assertSee('Guest Accessible Book');
    });
});

// Logged in user should see their library books on the books page

test('user can view their library books', function () {
    $user = User::factory()->create();
    $books = Book::factory()->count(2)->create();

    foreach ($books as $book) {
        $user->books()->attach($book);
    }

    $this->browse(function (Browser $browser) use ($user, $books) {
        $browser->loginAs($user)
            ->visit('/books')
            ->assertSee('Books');

        foreach ($books as $book) {
            $browser->assertSee($book->title);
        }
    });
});

// Logged in user can search for a book and see results

test('user can search for a book', function () {
    $user = User::factory()->create();

    $mock = Mockery::mock(SearchBooksFromApi::class);
    $mock->shouldReceive('run')
        ->with('harry potter', null, 10, 1)
        ->andReturn([
            'items' => [
                [
                    'identifier' => '9780747532743',
                    'title' => 'Harry Potter',
                    'authors' => [['name' => 'J.K. Rowling']],
                    'publishedDate' => '1997-06-26',
                    'description' => 'A young wizard adventure',
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

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/books/search')
            ->type('#query', 'harry potter')
            ->press('Search')
            ->waitForText('Harry Potter', 5)
            ->assertSee('Harry Potter');
    });
});

// Users can add a book to their library

test('user can add a book to their library', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->click('[data-slot="select-trigger"]')
            ->click('[data-slot="select-item"][value="Plan to Read"]')
            ->pause(500);
    });

    $this->assertDatabaseHas('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'Plan to Read',
    ]);
});

// Users can change a book status

test('user can change book status', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book, ['status' => 'Plan to Read']);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->click('[data-slot="select-trigger"]')
            ->click('[data-slot="select-item"][value="Completed"]')
            ->pause(500);
    });

    $this->assertDatabaseHas('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'Completed',
    ]);
});

// Users can add a note to a book

test('user can add a note to a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->type('#noteInput', 'A new note')
            ->press('Save')
            ->waitForText('A new note', 5);
    });

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'A new note',
    ]);
});

// Users can delete a note on a book

test('user can delete a note on a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book);

    $note = \App\Models\Note::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Delete me',
    ]);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->press('Delete')
            ->pressAndWaitFor('Delete')
            ->pause(500);
    });

    $this->assertDatabaseMissing('notes', [
        'id' => $note->id,
    ]);
});

// Users can add a review to a book

test('user can add a review to a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->press('Write a review')
            ->type('#reviewTitle', 'Great Book')
            ->type('#reviewContent', 'Loved it!')
            ->press('Submit Review')
            ->waitForText('Great Book', 5);
    });

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'title' => 'Great Book',
        'content' => 'Loved it!',
    ]);
});

// Users can update a review on a book

test('user can update a review on a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $review = \App\Models\Review::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'title' => 'Old Title',
        'content' => 'Old content',
    ]);

    $user->books()->attach($book);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->press('Edit review')
            ->type('#reviewTitle', 'Updated Title')
            ->type('#reviewContent', 'Updated content')
            ->press('Update Review')
            ->waitForText('Updated Title', 5);
    });

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'title' => 'Updated Title',
        'content' => 'Updated content',
    ]);
});

// Users can delete a review on a book

test('user can delete a review on a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book);

    $review = \App\Models\Review::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'title' => 'Delete Review',
    ]);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->press('Delete')
            ->pressAndWaitFor('Delete')
            ->pause(500);
    });

    $this->assertDatabaseMissing('reviews', [
        'id' => $review->id,
    ]);
});

// Users can update their rating of a book

test('user can update book rating', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book);

    $this->browse(function (Browser $browser) use ($user, $book) {
        $browser->loginAs($user)
            ->visit(route('books.show', $book))
            ->click('button[aria-label="Rate 4 star"]')
            ->pause(500)
            ->click('button[aria-label="Rate 2 star"]')
            ->pause(500);
    });

    $this->assertDatabaseHas('ratings', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'value' => 2,
    ]);
});

