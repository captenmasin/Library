<?php

use App\Contracts\BookApiServiceInterface;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\ImageTransformerController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UserBookController;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('library.index')
        : redirect()->route('login');
});

Route::get('test', function () {
    $booksApi = app(BookApiServiceInterface::class);
    Benchmark::dd([
        '1' => fn () => $booksApi->search(query: 'minecraft', maxResults: 1),
        '10' => fn () => $booksApi->search(query: 'minecraft', maxResults: 10),
        '50' => fn () => $booksApi->search(query: 'minecraft', maxResults: 50),
    ]);
})->name('test');

Route::prefix('books')->name('books.')
    ->controller(BookController::class)
    ->middleware(['auth'])
    ->group(function () {
        Route::get('search', 'index')->name('search');

        Route::get('{book}', 'show')->name('show')->withoutMiddleware(['auth']);
        Route::get('/preview/{identifier}', 'preview')->name('preview')->withoutMiddleware(['auth']);

        Route::patch('{book}', 'update')->name('update');

        Route::delete('{book}', 'destroy')->name('destroy');
    });

Route::prefix('{book}')
    ->middleware('auth')
    ->group(function () {
        Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
        Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

        Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::post('cover', [BookCoverController::class, 'update'])->name('cover.update');
        Route::delete('cover', [BookCoverController::class, 'destroy'])->name('cover.destroy');
    });

Route::prefix('library')->name('library.')
    ->controller(UserBookController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::post('/', 'store')->name('store');

        Route::put('{book:identifier}/tags', 'updateTags')->name('update_tags');

        Route::delete('{book:identifier}', 'destroy')->name('destroy');
    });

Route::name('user.')->middleware(['auth'])->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('library', [UserBookController::class, 'edit'])->name('library.edit');
        Route::get('danger', [ProfileController::class, 'danger'])->name('profile.danger');
        Route::get('password', [PasswordController::class, 'edit'])->name('password.edit');

        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::get('appearance', function () {
            return Inertia::render('settings/Appearance');
        })->name('appearance');
    });
});

Route::get('image-transform/{options}/{path}', ImageTransformerController::class)
    ->where('options', '([a-zA-Z]+=-?[a-zA-Z0-9]+,?)+')
    ->where('path', '.*\..*')
    ->name('image.transform');

require __DIR__.'/auth.php';
require __DIR__.'/testing.php';
