<?php

use App\Models\Lead;
use App\Models\User;
use Database\Seeders\SitePageSeeder;
use Livewire\Livewire;

it('creates a new lead with new status', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('company', 'Acme Inc.')
        ->set('website', 'https://example.com')
        ->set('service', 'Web Development')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit');

    expect(Lead::query()->count())
        ->toBe(1);

    $lead = Lead::query()->first();

    expect($lead->name)->toBe('John Doe')
        ->and($lead->email)->toBe('john@example.com')
        ->and($lead->company)->toBe('Acme Inc.')
        ->and($lead->website)->toBe('https://example.com')
        ->and($lead->service)->toBe('Web Development')
        ->and($lead->message)->toBe('I need a modern website for my business.')
        ->and($lead->status)->toBe('new');
});

it('requires a name when creating a lead', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('email', 'john@example.com')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit')
        ->assertHasErrors([
            'name' => 'required',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('requires an email when creating a lead', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit')
        ->assertHasErrors([
            'email' => 'required',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('requires a message when creating a lead', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->call('submit')
        ->assertHasErrors([
            'message' => 'required',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('rejects an invalid email when creating a lead', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'not-an-email')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit')
        ->assertHasErrors([
            'email' => 'email',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('rejects an invalid website when creating a lead', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('website', 'not-a-url')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit')
        ->assertHasErrors([
            'website' => 'url',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('rejects a message shorter than 20 characters', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('message', 'Too short')
        ->call('submit')
        ->assertHasErrors([
            'message' => 'min',
        ]);

    expect(Lead::query()->count())
        ->toBe(0);
});

it('accepts optional lead fields when they are empty', function () {
    $this->seed(SitePageSeeder::class);

    Livewire::test('pages::lets-talk')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('company', '')
        ->set('website', '')
        ->set('service', '')
        ->set('message', 'I need a modern website for my business.')
        ->call('submit');

    expect(Lead::query()->count())
        ->toBe(1);

    $lead = Lead::query()->first();

    expect($lead->company)->toBe('')
        ->and($lead->website)->toBe('')
        ->and($lead->service)->toBe('')
        ->and($lead->status)->toBe('new');
});

it('allows an admin to update a lead status', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create([
        'status' => 'new',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.lead', [
            'lead' => $lead,
        ])
        ->set('status', 'qualified')
        ->call('updateStatus');

    expect($lead->refresh()->status)
        ->toBe('qualified');
});

it('accepts every valid lead status', function (string $status) {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create([
        'status' => 'new',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.lead', [
            'lead' => $lead,
        ])
        ->set('status', $status)
        ->call('updateStatus');

    expect($lead->refresh()->status)
        ->toBe($status);
})->with([
    'new',
    'contacted',
    'qualified',
    'proposal',
    'won',
    'lost',
]);

it('rejects an invalid lead status', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create([
        'status' => 'new',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.lead', [
            'lead' => $lead,
        ])
        ->set('status', 'invalid-status')
        ->call('updateStatus')
        ->assertHasErrors([
            'status' => 'in',
        ]);

    expect($lead->refresh()->status)
        ->toBe('new');
});

it('updates only the lead status', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $lead = Lead::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'company' => 'Acme Inc.',
        'website' => 'https://example.com',
        'service' => 'Web Development',
        'message' => 'I need a modern website for my business.',
        'status' => 'new',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.lead', [
            'lead' => $lead,
        ])
        ->set('status', 'won')
        ->call('updateStatus');

    $lead->refresh();

    expect($lead->status)->toBe('won')
        ->and($lead->name)->toBe('John Doe')
        ->and($lead->email)->toBe('john@example.com')
        ->and($lead->company)->toBe('Acme Inc.')
        ->and($lead->website)->toBe('https://example.com')
        ->and($lead->service)->toBe('Web Development')
        ->and($lead->message)->toBe('I need a modern website for my business.');
});
