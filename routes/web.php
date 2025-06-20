<?php

use App\Actions\GetBookByBarcode;
use App\Actions\SearchBooksFromApi;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\ImageTransformerController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('image-transform/{options}/{path}', ImageTransformerController::class)
    ->where('options', '([a-zA-Z]+=-?[a-zA-Z0-9]+,?)+')
    ->where('path', '.*\..*')
    ->name('image.transform');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('books.index');
    } else {
        return redirect()->route('login');
    }
});

Route::put('books/{book}/cover', [BookCoverController::class, 'update'])->name('books.cover.update');
Route::delete('books/{book}/cover', [BookCoverController::class, 'destroy'])->name('books.cover.destroy');

Route::prefix('books')->name('books.')->controller(BookController::class)
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('{book}/edit', 'edit')->name('edit');

        Route::get('api/search', SearchBooksFromApi::class)->name('api.search');
        Route::get('api/barcode', GetBookByBarcode::class)->name('api.barcode');

        Route::post('/', 'store')->name('store');
        Route::post('{book}/read/toggle', 'toggleRead')->name('read.toggle');

        Route::post('{book}/notes', [NoteController::class, 'store'])
            ->name('notes.store');

        Route::post('{book}/reviews', [ReviewController::class, 'store'])
            ->name('reviews.store');

        Route::patch('{book}', 'update')->name('update');

        Route::delete('{book}', 'destroy')->name('destroy');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
