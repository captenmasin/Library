<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('user must be logged in to view dashboard', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/library')
            ->assertPathIs('/login');
    });
});

test('user can see dashboard', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user->id);

        $browser->visit('/library')
            ->assertSee('Books');
    });
});

test('user can see open new book modal', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user->id);

        $browser->visit('/library')
            ->press('Add Book')
            ->pause(500)
            ->assertSee('Add a new book to your library')
            ->type('#keyword-search', 'Jurassic Park');
    });
});
