<?php

use App\Models\User;

test('user can register and be redirected to books', function () {
    $this->browse(function ($browser) {
        $password = Str::random(16);
        $browser->visit('/register')
            ->type('#name', 'Test User')
            ->type('#username', 'testuser')
            ->type('#email', 'testuser@example.com')
            ->type('#password', $password)
            ->type('#password_confirmation', $password)
            ->press('Create account')
            ->waitForLocation('/books')
            ->assertPathIs('/books')
            ->assertSee('Books')
            ->fullLogout();
    });
});

test('user cannot register with mismatched passwords', function () {
    $this->browse(function ($browser) {
        $password = Str::random(16);
        $browser->visit('/register')
            ->type('#name', 'Mismatch User')
            ->type('#username', 'mismatch')
            ->type('#email', 'mismatch@example.com')
            ->type('#password', $password)
            ->type('#password_confirmation', 'wrongpass')
            ->press('Create account')
            ->waitForText('The password field confirmation does not match.', 5)
            ->assertSee('The password field confirmation does not match.');
    });
});

test('user cannot register without username', function () {
    $this->browse(function ($browser) {
        $password = Str::random(16);
        $browser->visit('/register')
            ->disableClientSideValidation()
            ->type('#name', 'Mismatch User')
            ->type('#email', 'mismatch@example.com')
            ->type('#password', $password)
            ->type('#password_confirmation', $password)
            ->press('Create account')
            ->waitForText('The username field is required.', 5)
            ->assertSee('The username field is required.');
    });
});

test('email must be unique', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->browse(function ($browser) {
        $password = Str::random(16);
        $browser->visit('/register')
            ->type('#name', 'Duplicate User')
            ->type('#username', \Illuminate\Support\Str::random())
            ->type('#email', 'existing@example.com')
            ->type('#password', $password)
            ->type('#password_confirmation', $password)
            ->press('Create account')
            ->waitForText('The email has already been taken', 5)
            ->assertSee('The email has already been taken');
    });
});

test('username must be unique', function () {
    User::factory()->create(['username' => 'existinguser']);

    $this->browse(function ($browser) {
        $password = Str::random(16);
        $browser->visit('/register')
            ->type('#name', 'Duplicate User')
            ->type('#username', 'existinguser')
            ->type('#email', 'existing@example.com')
            ->type('#password', $password)
            ->type('#password_confirmation', $password)
            ->press('Create account')
            ->waitForText('The username has already been taken.', 5)
            ->assertSee('The username has already been taken.');
    });
});
