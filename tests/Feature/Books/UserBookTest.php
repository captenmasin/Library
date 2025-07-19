<?php

use App\Models\Book;
use App\Models\User;
use App\Enums\UserBookStatus;

test('book can be removed from library', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

    $response = $this->actingAs($user)
        ->delete(route('api.user.books.destroy', $book));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);
});

// Test removing a book that is not in the user's library

test('removing missing book returns an error', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('api.user.books.destroy', $book));

    $response->assertForbidden();
});

test('book status can be updated', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

    $response = $this->actingAs($user)
        ->patch(route('api.user.books.update_status', $book), [
            'status' => UserBookStatus::Completed->name,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('book_user', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => UserBookStatus::Completed->name,
    ]);
});

test('status update fails when book is missing', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)
        ->patch(route('api.user.books.update_status', $book), [
            'status' => UserBookStatus::Completed->name,
        ]);

    $response->assertForbidden();
});

test('duplicate book cannot be added', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $user->books()->attach($book, ['status' => UserBookStatus::PlanToRead->value]);

    $response = $this->actingAs($user)
        ->post(route('api.user.books.store'), [
            'identifier' => $book->identifier,
            'status' => UserBookStatus::Reading->name,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseCount('book_user', 1);
});
