<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('user.books.index', absolute: false))
                    : Inertia::render('auth/VerifyEmail', ['status' => $request->session()->get('status')])
                        ->withMeta([
                            'title' => 'Verify your email address',
                            'description' => 'Please verify your email address to continue.',
                        ]);
    }
}
