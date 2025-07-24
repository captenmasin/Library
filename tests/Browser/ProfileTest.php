<?php

use App\Models\User;
use Laravel\Dusk\Browser;

// Users can update their profile information

test('user can update profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'username' => 'olduser',
        'email' => 'old@example.com',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/profile')
            ->type('#name', 'New Name')
            ->type('#username', 'newuser')
            ->type('#email', 'new@example.com')
            ->press('Save')
            ->waitForText('Saved.', 5);
    });

    $user->refresh();

    expect($user->name)->toBe('New Name');
    expect($user->username)->toBe('newuser');
    expect($user->email)->toBe('new@example.com');
});
