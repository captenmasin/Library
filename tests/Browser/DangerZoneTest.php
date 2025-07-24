<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('user can delete their account with the correct password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('delete-me'),
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/danger')
            ->pause(300) // UI transition
            ->press('Delete account')
            ->waitForText('Are you sure you want to delete your account?')
            ->assertSee('Are you sure you want to delete your account?')
            ->type('#password', 'delete-me')
            ->pause(500)
            ->click('#confirm-delete-account')
            ->pause(500)
            ->waitForLocation('/login')
            ->assertPathIs('/login');
    });

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

test('user sees error if password is incorrect', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/danger')
            ->pause(300)
            ->press('Delete account')
            ->waitForText('Are you sure you want to delete your account?')
            ->assertSee('Are you sure you want to delete your account?')
            ->type('#password', 'delete-me')
            ->click('#confirm-delete-account')
            ->waitForText('The password is incorrect')
            ->assertSee('The password is incorrect');
    });

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

test('user can cancel account deletion', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/settings/danger')
            ->press('Delete account')
            ->whenAvailable('[role="dialog"]', function (Browser $modal) {
                $modal->type('#password', 'anything')
                    ->press('Cancel');
            })
            ->pause(300)
            ->assertMissing('[role="dialog"]');
    });

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});
