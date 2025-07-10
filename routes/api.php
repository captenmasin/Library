<?php

use Illuminate\Http\Request;
use App\Actions\Books\AddBookToUser;
use Illuminate\Support\Facades\Http;
use App\Transformers\BookTransformer;
use Illuminate\Support\Facades\Route;
use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\RemoveBookFromUser;
use App\Actions\Books\SearchBooksFromApi;
use App\Actions\Users\UpdateUserSettings;
use App\Contracts\BookApiServiceInterface;
use App\Actions\Books\UpdateUserBookStatus;
use App\Actions\Users\UpdateSingleUserSetting;

Route::name('api.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('search', SearchBooksFromApi::class)->name('search');

        Route::get('test/{identifier}', function ($identifier, BookApiServiceInterface $booksApi) {
            $book = (new BookTransformer)::fromIsbn($booksApi::get($identifier));

            ImportBookFromData::dispatch(
                $book
            );

            return $book;
        })->name('test');

        Route::get('test2', function (Request $request) {
            $response = Http::withHeaders([
                'Authorization' => config('services.isbndb.key'),
            ])->get('https://api2.isbndb.com/author/'.urlencode($request->get('q')));

            return $response->json();
        })->name('test2');

        Route::get('fetch/{identifier}', FetchOrCreateBook::class)->name('fetch_or_create');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::patch('{book:identifier}/status', UpdateUserBookStatus::class)->name('books.update_status');

        Route::post('books', AddBookToUser::class)->name('books.store');
        Route::delete('{book:identifier}', RemoveBookFromUser::class)->name('books.destroy');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::patch('single', UpdateSingleUserSetting::class)->name('single.update');
            Route::patch('multiple', UpdateUserSettings::class)->name('multiple.update');
        });
    });
});
