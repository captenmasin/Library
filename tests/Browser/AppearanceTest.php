<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('user can toggle book tilting setting', function () {
    $user = User::factory()->create([
        'settings' => [
            'library' => [
                'tilt_books' => false,
            ],
        ],
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/appearance')
            ->pause(200)
            ->assertAriaAttribute('#library-tilt', 'checked', 'false') // off by default
            ->press('#library-tilt') // toggle on
            ->pause(200)
            ->assertAriaAttribute('#library-tilt', 'checked', 'true')
            ->press('#library-tilt') // toggle off
            ->pause(200);
    });

    $user->refresh();

    $userSettings = $user->settings ? json_decode($user->settings) : [];

    expect(data_get($userSettings, 'library.tilt_books'))->toBeFalse();
});

test('user can switch between light, dark, and system themes', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/appearance')
            ->pause(500)
            ->press('Dark')
            ->assertPlainCookieValue('appearance', 'dark')
            ->pause(300)
            ->press('Light')
            ->assertPlainCookieValue('appearance', 'light')
            ->pause(300)
            ->press('System')
            ->assertPlainCookieValue('appearance', 'system')
            ->pause(300);
        //            ->refresh()
        //            ->assertAttributeContains('@html', 'class', 'dark');
    });
});
