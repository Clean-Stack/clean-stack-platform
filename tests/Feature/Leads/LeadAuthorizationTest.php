<?php

use App\Models\Lead;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to access the leads index', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.leads'))
        ->assertOk();
});

it('prevents a regular user from accessing the leads index', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.leads'))
        ->assertForbidden();
});

it('redirects a guest to login when accessing the leads index', function () {
    $this->get(route('dashboard.leads'))
        ->assertRedirectToRoute('login');
});

it('allows an admin to view an existing lead', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard.leads.show', $lead))
        ->assertOk();
});

it('returns not found when an admin requests a non-existent lead', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.leads.show', 'does-not-exist'))
        ->assertNotFound();
});

it('prevents a regular user from viewing a lead', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $lead = Lead::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.leads.show', $lead))
        ->assertForbidden();
});

it('redirects a guest to login when viewing a lead', function () {
    $lead = Lead::factory()->create();

    $this->get(route('dashboard.leads.show', $lead))
        ->assertRedirectToRoute('login');
});

it('allows an admin to delete a lead', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.leads')
        ->call('confirmDelete', $lead->id)
        ->call('deleteLead');

    expect(Lead::query()->whereKey($lead->id)->exists())
        ->toBeFalse();
});

it('persists the admin middleware for Livewire requests', function () {
    expect(Livewire::getPersistentMiddleware())
        ->toContain(\App\Http\Middleware\EnsureUserIsAdmin::class);
});

