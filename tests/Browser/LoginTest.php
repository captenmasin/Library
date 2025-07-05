<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('user can login and be redirected to books', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $browser->visit('/login')
            ->assertSee('Log in')
            ->type('#email', $user->email)
            ->type('#password', 'password')
            ->press('Log in')
            ->waitForLocation('/library')
            ->assertPathIs('/library')
            ->assertSee('Books')
            ->fullLogout();

        $browser->visit('/test-logout');
    });
});

test('user cannot login with invalid credentials', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $browser->visit('/login')
            ->assertSee('Log in')
            ->waitFor('#email')
            ->type('#email', $user->email)
            ->type('#password', 'wrong-password')
            ->pressAndWaitFor('Log in')
            ->assertSee('These credentials do not match our records.');

        $browser->visit('/login')
            ->assertSee('Log in')
            ->waitFor('#email')
            ->type('#email', 'wrongemail@example.com')
            ->type('#password', 'wrong-password')
            ->pressAndWaitFor('Log in')
            ->assertSee('These credentials do not match our records.');
    });
});

test('email and password are required', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->press('Log in')
            ->waitForText('The email field is required')
            ->assertSee('The email field is required')
            ->assertSee('The password field is required');

        $browser->visit('/login')
            ->press('Log in')
            ->type('#email', 'email@example.com')
            ->waitForText('The password field is required')
            ->assertSee('The password field is required');
    });
});
