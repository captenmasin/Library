<?php

use Inertia\Inertia;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteController;
use App\Contracts\BookApiServiceInterface;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\ImageTransformerController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PasswordController;

Horizon::auth(fn ($request) => Gate::check('viewHorizon', [$request->user()]));

// Homepage
Route::get('/', HomeController::class)->middleware(['auth'])->name('home');

// Test benchmarking route
Route::get('test', function () {
    $booksApi = app(BookApiServiceInterface::class);

    Benchmark::dd([
        '1' => fn () => $booksApi->search(query: 'minecraft', maxResults: 1),
        '10' => fn () => $booksApi->search(query: 'minecraft', maxResults: 10),
        '50' => fn () => $booksApi->search(query: 'minecraft', maxResults: 50),
    ]);
})->name('test');

// Book routes
Route::prefix('books')
    ->name('books.')
    ->controller(BookController::class)
    ->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('search', 'index')->name('search');
            Route::patch('{book}', 'update')->name('update');
            Route::delete('{book}', 'destroy')->name('destroy');
        });

        Route::get('{book}', 'show')->name('show');
        Route::get('preview/{identifier}', 'preview')->name('preview');
    });

// Book-related sub-resources (notes, reviews, cover)
Route::prefix('{book}')->middleware('auth')->group(function () {
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::post('ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::put('ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');

    Route::post('cover', [BookCoverController::class, 'update'])->name('cover.update');
    Route::delete('cover', [BookCoverController::class, 'destroy'])->name('cover.destroy');
});

// Authenticated user routes
Route::middleware('auth')->name('user.')->group(function () {
    Route::prefix('books')->name('books.')->controller(UserBookController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('{book:identifier}/tags', 'updateTags')->name('update_tags');
    });

    // Authenticated user settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('danger', [ProfileController::class, 'danger'])->name('profile.danger');
        Route::get('books', [UserBookController::class, 'edit'])->name('books.edit');

        Route::get('password', [PasswordController::class, 'edit'])->name('password.edit');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::get('appearance', fn () => Inertia::render('settings/Appearance'))->name('appearance');
    });
});

// Dynamic image transformation
Route::get('image-transform/{options}/{path}', ImageTransformerController::class)
    ->where('options', '([a-zA-Z]+=-?[a-zA-Z0-9]+,?)+')
    ->where('path', '.*\..*')
    ->name('image.transform');

// Auth and test-only routes
require __DIR__.'/auth.php';
require __DIR__.'/testing.php';
