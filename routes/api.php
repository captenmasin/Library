<?php

use App\Actions\Books\FetchOrCreateBook;
use App\Actions\Books\GetBookByBarcode;
use App\Actions\Books\ImportBookFromData;
use App\Actions\Books\SearchBooksFromApi;
use App\Actions\Users\UpdateSingleUserSetting;
use App\Actions\Users\UpdateUserSettings;
use App\Contracts\BookApiServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('search', SearchBooksFromApi::class)->name('search');
        //        Route::get('barcode', GetBookByBarcode::class)->name('get_by_barcode');

        Route::get('test/{identifier}', function ($identifier, BookApiServiceInterface $booksApi) {
            $book = $booksApi::get($identifier);

            ImportBookFromData::dispatch(
                $book['identifier'],
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

        Route::get('{identifier}', FetchOrCreateBook::class)->name('fetch_or_create');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::patch('single', UpdateSingleUserSetting::class)->name('single.update');
            Route::patch('multiple', UpdateUserSettings::class)->name('multiple.update');
        });
    });
});
