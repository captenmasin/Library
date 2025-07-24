<?php

use App\Models\User;
use Laravel\Dusk\Browser;

// Users can update their profile information
test('user can update basic profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'username' => 'olduser',
        'email' => 'old@example.com',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->pause(500)
            ->type('#name', 'New Name')
            ->type('#username', 'newuser')
            ->type('#email', 'new@example.com')
            ->press('Save')
            ->waitForText('Profile updated successfully', 5)
            ->waitForText('Your email address is unverified', 5);
    });

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->username)->toBe('newuser')
        ->and($user->email)->toBe('new@example.com');
});

test('username must be unique', function () {
    User::factory()->create(['username' => 'takenuser']);
    $user = User::factory()->create(['username' => 'originaluser']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->type('#username', 'takenuser')
            ->press('Save')
            ->waitForText('The username has already been taken')
            ->assertSee('The username has already been taken');
    });
});

test('avatar must be an image', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->attach('#avatar', __DIR__.'/fixtures/text-file.txt')
            ->press('Save')
            ->waitForText('The avatar field must be an image');
    });
});

test('user can remove avatar', function () {
    $user = User::factory()->create();

    $avatarFile = __DIR__.'/fixtures/avatar.png';

    // copy file to a new name to avoid issues with Dusk
    copy($avatarFile, __DIR__.'/fixtures/avatar_copy.png');

    $user->addMedia(__DIR__.'/fixtures/avatar_copy.png')
        ->toMediaCollection('avatar');

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->press('Remove avatar')
            ->pause(200);
    });

    $user->refresh();
    expect($user->avatar)->toBe('');
});

test('user can request a new verification email', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->pause(500)
            ->type('#email', 'new@example.com')
            ->press('Save')
            ->waitForText('Your email address is unverified', 5)
            ->press('Click here to resend the verification email.')
            ->waitForText('A new verification link has been sent to your email address.', 5)
            ->assertSee('A new verification link has been sent to your email address.');
    });

    $user->refresh();

    expect($user->email)->toBe('new@example.com')
        ->and($user->hasVerifiedEmail())->toBeFalse();
});

test('user can update avatar', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->pause(500)
            ->attach('#avatar', __DIR__.'/fixtures/avatar.png')
            ->press('Save')
            ->waitForText('Profile updated successfully', 5);
    });

    $user->refresh();

    expect($user->avatar)->not->toBe('');
});
