<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to access the categories index', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.categories.index'))
        ->assertOk();
});

it('prevents a regular user from accessing the categories index', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.categories.index'))
        ->assertForbidden();
});

it('redirects a guest to login when accessing the categories index', function () {
    $this->get(route('dashboard.categories.index'))
        ->assertRedirectToRoute('login');
});

it('allows an admin to edit an existing category', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create([
        'slug' => 'security-category',
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.categories.edit', $category))
        ->assertOk();
});

it('returns not found when an admin requests a non-existent category', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.categories.edit', 'does-not-exist'))
        ->assertNotFound();
});

it('prevents a regular user from editing a category', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $category = Category::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.categories.edit', $category))
        ->assertForbidden();
});

it('does not delete a category that contains posts', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Post::factory()->create([
        'category_id' => $category->id,
        'user_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.categories.index')
        ->call('confirmDelete', $category->id)
        ->call('deleteCategory');

    expect(Category::query()->whereKey($category->id)->exists())
        ->toBeTrue();
});

it('deletes a category that contains no posts', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.categories.index')
        ->call('confirmDelete', $category->id)
        ->call('deleteCategory');

    expect(Category::query()->whereKey($category->id)->exists())
        ->toBeFalse();
});

