<?php

use App\Actions\Books\AddBookToUser;
use Illuminate\Support\Facades\Route;
use App\Actions\Books\FetchOrCreateBook;
use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Actions\Books\RemoveBookFromUser;
use App\Actions\Books\SearchBooksFromApi;
use App\Actions\Users\UpdateUserSettings;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReviewController;
use App\Actions\Books\UpdateUserBookStatus;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\DashboardController;
use App\Actions\Users\UpdateSingleUserSetting;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::name('api.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'show'])->name('user.show');
        Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('user/profile', [ProfileController::class, 'update'])->name('user.profile.update');
        Route::delete('user/avatar', [ProfileController::class, 'destroyAvatar'])->name('user.avatar.destroy');
        Route::delete('user', [ProfileController::class, 'destroy'])->name('user.destroy');
        Route::put('user/password', [PasswordController::class, 'update'])->name('user.password.update');

        Route::prefix('user/settings')->name('user.settings.')->group(function () {
            Route::patch('single', UpdateSingleUserSetting::class)->name('single.update');
            Route::patch('multiple', UpdateUserSettings::class)->name('multiple.update');
        });

        Route::middleware('verified')->group(function () {
            Route::get('dashboard', DashboardController::class)->name('dashboard');

            Route::prefix('books')->name('books.')->group(function () {
                Route::get('search', SearchBooksFromApi::class)->name('search');
                Route::post('resolve', FetchOrCreateBook::class)->name('resolve');
                Route::get('fetch/{identifier}', FetchOrCreateBook::class)->name('fetch_or_create');
                Route::get('{book:identifier}', [BookController::class, 'apiShow'])->name('show');

                Route::scopeBindings()->prefix('{book:identifier}')->group(function () {
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
            });

            Route::prefix('user')->name('user.')->group(function () {
                Route::get('books', [UserBookController::class, 'index'])->name('books.index');
                Route::post('books', AddBookToUser::class)->name('books.store');
                Route::patch('{book:identifier}/status', UpdateUserBookStatus::class)->name('books.update_status');
                Route::put('{book:identifier}/tags', [UserBookController::class, 'updateTags'])->name('books.update_tags');
                Route::delete('{book:identifier}', RemoveBookFromUser::class)->name('books.destroy');

                Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
                Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
                Route::get('activities', [ActivitiesController::class, 'index'])->name('activities.index');
            });
        });
    });
});
