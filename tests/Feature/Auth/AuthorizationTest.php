<?php

use App\Models\User;

it('allows a regular user to access the dashboard', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('prevents a regular user from accessing admin routes', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.index'))
        ->assertForbidden();
});

it('allows an admin user to access the dashboard', function () {
    $user = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('allows an admin user to access admin routes', function () {
    $user = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.index'))
        ->assertOk();
});

it('redirects a guest user to the login page when accessing the dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirectToRoute('login');
});
it('prevents mass assignment of the admin flag', function () {
    $user = User::create([
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => 'password',
        'is_admin' => true,
    ]);

    expect($user->getAttribute('is_admin'))->toBeNull();
});
