<?php

use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to access the services index', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.services.index'))
        ->assertOk();
});

it('prevents a regular user from accessing the services index', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.services.index'))
        ->assertForbidden();
});

it('redirects a guest to login when accessing the services index', function () {
    $this->get(route('dashboard.services.index'))
        ->assertRedirectToRoute('login');
});

it('allows an admin to edit an existing service', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $service = Service::factory()->create([
        'slug' => 'security-service',
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.services.edit', $service))
        ->assertOk();
});

it('returns not found when an admin requests a non-existent service', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.services.edit', 'does-not-exist'))
        ->assertNotFound();
});

it('prevents a regular user from editing a service', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $service = Service::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.services.edit', $service))
        ->assertForbidden();
});

it('redirects a guest to login when editing a service', function () {
    $service = Service::factory()->create();

    $this->get(route('dashboard.services.edit', $service))
        ->assertRedirectToRoute('login');
});

it('allows an admin to delete a service', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $service = Service::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.services.index')
        ->call('confirmDelete', $service->id)
        ->call('deleteService');

    expect(Service::query()->whereKey($service->id)->exists())
        ->toBeFalse();
});

it('persists the admin middleware for Livewire requests', function () {
    expect(\Livewire\Livewire::getPersistentMiddleware())
        ->toContain(\App\Http\Middleware\EnsureUserIsAdmin::class);
});
