<?php

use App\Actions\Books\FetchBookByIdentifier;
use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\GetBookByBarcode;
use App\Actions\Books\SearchBooksFromApi;
use App\Actions\Users\UpdateUserSettings;
use App\Contracts\BookApiServiceInterface;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('search', SearchBooksFromApi::class)->name('search');
        //        Route::get('barcode', GetBookByBarcode::class)->name('get_by_barcode');

        Route::get('test/{identifier}', function ($identifier, BookApiServiceInterface $booksApi) {
            return $booksApi::get($identifier);
        })->name('test');

        Route::get('identifier/{identifier}', FetchBookByIdentifier::class)->name('fetch_by_identifier');

        Route::post('/', FetchOrCreateBook::class)->name('fetch_or_create');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::patch('/', UpdateUserSettings::class)->name('update');
        });
    });
});
