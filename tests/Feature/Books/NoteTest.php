<?php

use App\Models\Book;
use App\Models\User;
use App\Models\Note;

// Test that a user can add a note to a book

test('user can add a note to a book', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('notes.store', $book->path), [
            'content' => 'My personal note',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'My personal note',
    ]);
});

// Test that posting again updates the existing note

test('user can update a note by posting again', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    Note::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Old note',
    ]);

    $response = $this->actingAs($user)
        ->post(route('notes.store', $book->path), [
            'content' => 'Updated note',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseCount('notes', 1);
    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Updated note',
    ]);
});

// Test that a user can delete their note

test('user can delete their note', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $note = Note::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Delete me',
    ]);

    $response = $this->actingAs($user)
        ->delete("/{$book->path}/notes/{$note->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

// Test that a user cannot delete someone else's note

test("user cannot delete another user's note", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $book = Book::factory()->create();

    $note = Note::create([
        'user_id' => $otherUser->id,
        'book_id' => $book->id,
        'content' => 'Should not delete',
    ]);

    $response = $this->actingAs($user)
        ->delete("/{$book->path}/notes/{$note->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('notes', ['id' => $note->id]);
});

// Test that storing notes requires authentication

test('notes require authentication', function () {
    $book = Book::factory()->create();
    $note = Note::create([
        'user_id' => User::factory()->create()->id,
        'book_id' => $book->id,
        'content' => 'content',
    ]);

    $this->post(route('notes.store', $book->path), ['content' => 'guest note'])
        ->assertRedirect(route('login'));

    $this->delete("/{$book->path}/notes/{$note->id}")
        ->assertRedirect(route('login'));
});

