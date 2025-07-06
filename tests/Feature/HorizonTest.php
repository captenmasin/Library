<?php

it('non-authed user cannot access horizon page', function () {
    $response = $this->get('/horizon');

    $response->assertStatus(403);
});

it('authed non-admin user can access horizon page', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $response = $this->get('/horizon');

    $response->assertStatus(403);
});

it('authed admin user can access horizon page', function () {
    $this->seed();

    $this->actingAs(\App\Models\User::factory()->create()->assignRole(\App\Enums\UserRole::Admin));

    $response = $this->get('/horizon');

    $response->assertStatus(200);
});
