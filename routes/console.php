<?php

use App\Models\Book;
use App\Models\Cover;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    Book::all()->each(function ($book) {
        $book->slug = \Illuminate\Support\Str::slug($book->title);
        $book->saveQuietly();
    });
});

Artisan::command('make:admin', function () {
    $name = \Laravel\Prompts\text('Name:');
    $username = \Laravel\Prompts\text('Username:');
    $email = \Laravel\Prompts\text('Email:');
    $password = \Laravel\Prompts\password('Password:');

    $user = \App\Models\User::create([
        'name' => $name,
        'username' => $username,
        'email' => $email,
        'password' => bcrypt($password),
    ]);

    $user->verifyEmail();

    $user->assignRole(\App\Enums\UserRole::User);
    $user->assignRole(\App\Enums\UserRole::Admin);
});

Artisan::command('reset', function () {
    Book::all()->each(fn ($book) => $book->delete());
    Cover::all()->each(fn ($book) => $book->delete());
    Media::where('model_type', Cover::class)->get()->each(fn ($book) => $book->delete());
    DB::table('book_user')->truncate();
    DB::table('author_book')->truncate();
    DB::table('authors')->truncate();
});
