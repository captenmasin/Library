<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Inertia\Response;
use App\Actions\TrackEvent;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\Settings\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Settings', 'href' => route('user.settings.profile.edit')],
            ],
        ])->withMeta([
            'title' => 'Profile Settings',
            'description' => 'Manage your profile information, including your name, email, and avatar.',
        ]);
    }

    public function danger(Request $request): Response
    {
        return Inertia::render('settings/Danger', [
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Settings', 'href' => route('user.settings.profile.edit')],
                ['title' => 'Danger zone', 'href' => route('user.settings.profile.danger')],
            ],
        ])
            ->withMeta([
                'title' => 'Danger Zone',
                'description' => 'Delete your account.',
            ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse|RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
            $request->user()->sendEmailVerificationNotification();
        }

        if ($request->file('avatar')) {
            $request->user()->addMedia($request->file('avatar'))
                ->toMediaCollection('avatar');

            TrackEvent::dispatchAfterResponse(AnalyticsEvent::UserAccountAvatarUpdated, [
                'user_id' => $request->user()?->id,
            ]);
        }

        if ($request->filled('profile_colour')) {
            $request->user()->settings()->update('profile.colour', $request->input('profile_colour'));
            TrackEvent::dispatchAfterResponse(AnalyticsEvent::UserAccountProfileColourUpdated, [
                'user_id' => $request->user()?->id,
                'profile_colour' => $request->input('profile_colour'),
            ]);
        }

        $request->user()->save();

        return $request->wantsJson()
            ? response()->json((new UserResource($request->user()->withoutRelations()))->asUser())
            : to_route('user.settings.profile.edit');
    }

    public function destroyAvatar(Request $request): HttpResponse|RedirectResponse
    {
        $request->user()->clearMediaCollection('avatar');

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::UserAccountAvatarRemoved, [
            'user_id' => $request->user()?->id,
        ]);

        return $request->wantsJson()
            ? response()->noContent()
            : redirect()->back()->with('success', 'Your avatar has been deleted.');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): HttpResponse|RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        TrackEvent::dispatchAfterResponse(AnalyticsEvent::UserAccountDeleted, [
            'user_id' => $request->user()?->id,
        ]);

        if ($request->wantsJson()) {
            $user->tokens()->delete();
            $user->delete();

            return response()->noContent();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
