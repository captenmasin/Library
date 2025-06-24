<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (app()->environment('local', 'testing')) {
    Route::get('/test-logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    });
}
