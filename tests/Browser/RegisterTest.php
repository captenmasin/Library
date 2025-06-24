<?php

use App\Models\User;

test('user can register and be redirected to books', function () {
    $this->browse(function ($browser) {
        $browser->visit('/register')
            ->type('#name', 'Test User')
            ->type('#email', 'testuser@example.com')
            ->type('#password', 'secret123')
            ->type('#password_confirmation', 'secret123')
            ->press('Create account')
            ->waitForLocation('/books')
            ->assertPathIs('/books')
            ->assertSee('Books')
            ->fullLogout();
    });
});

// 2. user cannot register with mismatched passwords
test('user cannot register with mismatched passwords', function () {
    $this->browse(function ($browser) {
        $browser->visit('/register')
            ->type('#name', 'Mismatch User')
            ->type('#email', 'mismatch@example.com')
            ->type('#password', 'secret123')
            ->type('#password_confirmation', 'wrongpass')
            ->press('Create account')
            ->waitForText('The password field confirmation does not match.')
            ->assertSee('The password field confirmation does not match.');
    });
});

test('email must be unique', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->browse(function ($browser) {
        $browser->visit('/register')
            ->type('#name', 'Duplicate User')
            ->type('#email', 'existing@example.com')
            ->type('#password', 'secret123')
            ->type('#password_confirmation', 'secret123')
            ->press('Create account')
            ->waitForText('The email has already been taken')
            ->assertSee('The email has already been taken');
    });
});
