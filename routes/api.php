<?php

use App\Actions\CreateOrFetchBook;
use App\Actions\FetchBookByIdentifier;
use App\Actions\GetBookByBarcode;
use App\Actions\SearchBooksFromApi;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('search', SearchBooksFromApi::class)->name('search');
        Route::get('barcode', GetBookByBarcode::class)->name('get_by_barcode');

        Route::get('test/{identifier}', function ($identifier) {
            return \App\Services\GoogleBooksService::get($identifier);
        })->name('test');

        Route::get('identifier/{identifier}', FetchBookByIdentifier::class)->name('fetch_by_identifier');

        Route::post('/', CreateOrFetchBook::class)->name('create_or_fetch');
    });
});
