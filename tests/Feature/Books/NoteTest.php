<?php

use App\Models\Book;
use App\Models\Note;
use App\Models\User;
use App\Enums\UserBookStatus;

// Test that a user can add a note to a book

test('user cannot add a note to a book not in their library', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('notes.store', $book->path), [
            'content' => 'My personal note',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'My personal note',
    ]);
});

test('user can add a note to a book in their library', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book->id, ['status' => UserBookStatus::Reading]);

    $response = $this->actingAs($user)
        ->post(route('notes.store', $book->path), [
            'content' => 'My personal note',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'My personal note',
        'book_status' => UserBookStatus::Reading,
    ]);
});

test('note content is required', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book->id, ['status' => UserBookStatus::Reading]);

    $response = $this->actingAs($user)
        ->post(route('notes.store', $book->path), [
            'content' => '',
        ]);

    $response->assertSessionHasErrors('content');
    $this->assertDatabaseMissing('notes', [
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);
});

// Test that a user can delete their note
test('user can delete their note', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $note = Note::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Delete me',
    ]);

    $response = $this->actingAs($user)
        ->delete(route('notes.destroy', [$book->path, $note->id]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

// Test that a user cannot delete someone else's note
test("user cannot delete another user's note", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $book = Book::factory()->create();

    $note = Note::factory()->create([
        'user_id' => $otherUser->id,
        'book_id' => $book->id,
        'content' => 'Should not delete',
    ]);

    $response = $this->actingAs($user)
        ->delete(route('notes.destroy', [$book->path, $note->id]));

    $response->assertForbidden();
    $this->assertDatabaseHas('notes', ['id' => $note->id]);
});

// Test that storing notes requires authentication
test('notes require authentication', function () {
    $book = Book::factory()->create();
    $note = Note::factory()->create([
        'user_id' => User::factory()->create()->id,
        'book_id' => $book->id,
        'content' => 'content',
    ]);

    $this->post(route('notes.store', $book->path), ['content' => 'guest note'])
        ->assertRedirect(route('login'));

    $this->delete(route('notes.destroy', [$book->path, $note->id]))
        ->assertRedirect(route('login'));
});

test('user can update their note', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $note = Note::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Old content',
    ]);

    $this->actingAs($user)
        ->put(route('notes.update', [$book->path, $note->id]), [
            'content' => 'Updated content',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'content' => 'Updated content',
    ]);
})->todo();
