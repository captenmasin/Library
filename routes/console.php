<?php

use App\Models\Tag;
use App\Models\Book;
use App\Models\User;
use App\Models\Cover;
use App\Models\Publisher;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ConfigureRelatedBooks;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Schedule::command(ConfigureRelatedBooks::class)->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin', function () {
    $name = \Laravel\Prompts\text('Name:');
    $username = \Laravel\Prompts\text('Username:');
    $email = \Laravel\Prompts\text('Email:');
    $password = \Laravel\Prompts\password('Password:');

    $user = User::create([
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
    Cover::all()->each(fn ($cover) => $cover->delete());
    Tag::all()->each(fn ($tag) => $tag->delete());
    Publisher::all()->each(fn ($publisher) => $publisher->delete());
    Media::where('model_type', Cover::class)->get()->each(fn ($book) => $book->delete());
    DB::table('book_user')->truncate();
    DB::table('author_book')->truncate();
    DB::table('authors')->truncate();
    DB::table('book_tag')->truncate();
});
