<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        $redirectUrl = $request->input('redirect', null);

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'redirect' => $redirectUrl,
            'status' => $request->session()->get('status'),
        ])->withMeta([
            'title' => 'Log in to your account',
            'description' => 'Enter your email/username and password below to log in.',
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->has('redirect')) {
            $redirectUrl = $request->input('redirect');
            if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                return redirect()->to($redirectUrl);
            }
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
