<?php

use App\Models\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withToken;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

describe('mobile API authentication', function () {
    it('logs in with email or username', function (string $credential) {
        $user = User::factory()->create();

        $response = postJson(route('api.login', absolute: false), [
            'login' => $user->{$credential},
            'password' => 'password',
            'device_name' => 'Test phone',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);

        $accessToken = PersonalAccessToken::findToken($response->json('token'));

        expect($accessToken)->not->toBeNull()
            ->and($accessToken->tokenable->is($user))->toBeTrue()
            ->and($accessToken->name)->toBe('Test phone');
    })->with(['email', 'username']);

    it('validates required login fields without issuing a token', function () {
        postJson(route('api.login', absolute: false))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['login', 'password', 'device_name']);

        expect(PersonalAccessToken::query()->count())->toBe(0);
    });

    it('rejects invalid credentials without issuing a token', function () {
        $user = User::factory()->create();

        postJson(route('api.login', absolute: false), [
            'login' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'Test phone',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('login');

        expect($user->tokens()->count())->toBe(0);
    });

    it('rate limits repeated invalid credential attempts', function () {
        $user = User::factory()->create();
        $credentials = [
            'login' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'Test phone',
        ];

        foreach (range(1, 5) as $_) {
            postJson(route('api.login', absolute: false), $credentials)->assertUnprocessable();
        }

        $response = postJson(route('api.login', absolute: false), $credentials)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('login');

        expect($response->json('errors.login.0'))->toContain('Too many login attempts')
            ->and($user->tokens()->count())->toBe(0);
    });

    it('requires a bearer token to retrieve the current user', function () {
        $user = User::factory()->create();
        $token = $user->createToken('Test phone')->plainTextToken;

        getJson(route('api.user.show', absolute: false))->assertUnauthorized();

        withToken($token)
            ->getJson(route('api.user.show', absolute: false))
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    });

    it('logs out only the current token and rejects it afterward', function () {
        $user = User::factory()->create();
        $currentToken = $user->createToken('Current phone');
        $otherToken = $user->createToken('Other phone');

        withToken($currentToken->plainTextToken)
            ->deleteJson(route('api.logout', absolute: false))
            ->assertNoContent();

        expect(PersonalAccessToken::findToken($currentToken->plainTextToken))->toBeNull()
            ->and(PersonalAccessToken::findToken($otherToken->plainTextToken))->not->toBeNull();

        Auth::guard('sanctum')->forgetUser();

        withToken($currentToken->plainTextToken)
            ->getJson(route('api.user.show', absolute: false))
            ->assertUnauthorized();

        withToken($otherToken->plainTextToken)
            ->getJson(route('api.user.show', absolute: false))
            ->assertOk();
    });

    it('returns JSON when an unverified user accesses a verified endpoint', function () {
        $user = User::factory()->unverified()->create();
        $token = $user->createToken('Test phone')->plainTextToken;

        withToken($token)
            ->getJson(route('api.dashboard', absolute: false))
            ->assertForbidden()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Your email address is not verified.',
            ]);
    });

    it('resends verification email for an authenticated unverified user', function () {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        withToken($user->createToken('Test phone')->plainTextToken)
            ->postJson(route('api.verification.send', absolute: false))
            ->assertOk()
            ->assertJsonPath('message', 'Verification link sent.');

        Notification::assertSentTo($user, VerifyEmail::class);
    });
});
