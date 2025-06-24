<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\ImageTransformerController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserBookController;
use Illuminate\Support\Facades\Route;

Route::get('image-transform/{options}/{path}', ImageTransformerController::class)
    ->where('options', '([a-zA-Z]+=-?[a-zA-Z0-9]+,?)+')
    ->where('path', '.*\..*')
    ->name('image.transform');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('books.index')
        : redirect()->route('login');
});

Route::put('books/{book}/cover', [BookCoverController::class, 'update'])->name('books.cover.update');
Route::delete('books/{book}/cover', [BookCoverController::class, 'destroy'])->name('books.cover.destroy');

Route::prefix('books')->name('books.')->controller(BookController::class)->middleware(['auth'])
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('{book}', 'show')->name('show')
            ->withoutMiddleware(['auth']);

        Route::post('{book}/notes', [NoteController::class, 'store'])
            ->name('notes.store');

        Route::post('{book}/reviews', [ReviewController::class, 'store'])
            ->name('reviews.store');

        Route::patch('{book}', 'update')->name('update');

        Route::delete('{book}', 'destroy')->name('destroy');
    });

Route::prefix('users')->name('users.')->middleware(['auth'])->group(function () {
    Route::prefix('books')->name('books.')->controller(UserBookController::class)->group(function () {
        Route::post('/', 'store')->name('store');

        Route::patch('{book:identifier}/status', 'updateStatus')->name('update_status');

        Route::put('{book:identifier}/tags', 'updateTags')->name('update_tags');

        Route::delete('{book:identifier}', 'destroy')->name('destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/testing.php';
