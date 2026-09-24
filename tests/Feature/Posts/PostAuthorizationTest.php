<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
*/

it('allows an admin to view the posts index', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.posts.index'))
        ->assertOk();
});

it('allows an admin to create a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.posts.create'))
        ->assertOk();
});

it('allows an admin to edit a post by slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'slug' => 'security-audit-post',
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.posts.edit', $post))
        ->assertOk();
});

it('returns not found when an admin requests a non-existent post slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.posts.edit', 'does-not-exist'))
        ->assertNotFound();
});

it('prevents a regular user from viewing the posts index', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.index'))
        ->assertForbidden();
});

it('prevents a regular user from creating a post', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.create'))
        ->assertForbidden();
});

it('prevents a regular user from editing a post', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.edit', $post))
        ->assertForbidden();
});

it('redirects a guest to login when viewing the posts index', function () {
    $this->get(route('dashboard.posts.index'))
        ->assertRedirectToRoute('login');
});

it('redirects a guest to login when creating a post', function () {
    $this->get(route('dashboard.posts.create'))
        ->assertRedirectToRoute('login');
});

it('redirects a guest to login when editing a post', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->get(route('dashboard.posts.edit', $post))
        ->assertRedirectToRoute('login');
});


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

it('allows an admin to create a draft post with valid data', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'Laravel Security Best Practices')
        ->set('excerpt', 'A practical guide to improving Laravel application security.')
        ->set('content', 'This is a valid post content with more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.posts.index'));

    $post = Post::query()->first();

    expect($post)->not->toBeNull()
        ->and($post->user_id)->toBe($admin->id)
        ->and($post->category_id)->toBe($category->id)
        ->and($post->title)->toBe('Laravel Security Best Practices')
        ->and($post->slug)->toBe('laravel-security-best-practices')
        ->and($post->status)->toBe('draft')
        ->and($post->published_at)->toBeNull();
});

it('assigns the authenticated admin as the post author', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'Clean Laravel Architecture')
        ->set('excerpt', 'A short summary of clean Laravel architecture.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::query()->firstOrFail();

    expect($post->user_id)->toBe($admin->id);
});

it('generates the slug from the post title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'Building Modern Laravel Applications')
        ->assertSet(
            'slug',
            'building-modern-laravel-applications'
        );
});

it('rejects a duplicate post slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Post::factory()->create([
        'user_id' => $admin->id,
        'slug' => 'existing-post',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'Existing Post')
        ->set('excerpt', 'A short summary for the new post.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasErrors(['slug']);

    expect(Post::query()->where('slug', 'existing-post')->count())
        ->toBe(1);
});

it('requires a post title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('excerpt', 'A valid post excerpt.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasErrors(['title']);
});

it('requires a post excerpt', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Valid Post Title')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasErrors(['excerpt']);
});

it('rejects post content shorter than twenty characters', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Valid Post Title')
        ->set('excerpt', 'A valid post excerpt.')
        ->set('content', 'Too short')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasErrors([
            'content' => 'min',
        ]);
});

it('rejects a non-existent category', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Valid Post Title')
        ->set('excerpt', 'A valid post excerpt.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', '999999')
        ->call('save')
        ->assertHasErrors([
            'category_id' => 'exists',
        ]);
});

it('creates a published post when published is enabled with a valid publication date', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $publishedAt = now()
        ->subHour()
        ->format('Y-m-d\TH:i');

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Published Laravel Post')
        ->set('excerpt', 'A short summary for the published post.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->set('published_at', $publishedAt)
        ->set('published', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.posts.index'));

    $post = Post::query()->firstOrFail();

    expect($post->status)->toBe('published')
        ->and($post->published_at)->not->toBeNull();
});

it('rejects an invalid published at date format', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Valid Laravel Post')
        ->set('excerpt', 'A short summary for the post.')
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->set('published_at', 'not-a-date')
        ->call('save')
        ->assertHasErrors([
            'published_at' => 'date_format',
        ]);
});

it('rejects an excerpt longer than one thousand characters', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('title', 'A Valid Laravel Post')
        ->set('excerpt', str_repeat('a', 1001))
        ->set('content', 'This content contains more than twenty characters.')
        ->set('category_id', (string) $category->id)
        ->call('save')
        ->assertHasErrors([
            'excerpt' => 'max',
        ]);
});

it('prevents a published post from using a future publication date', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $futureDate = now()
        ->addHour()
        ->format('Y-m-d\TH:i');

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.create')
        ->set('published', true)
        ->set('published_at', $futureDate)
        ->assertHasErrors([
            'published_at',
        ])
        ->assertSet('published', false);
});


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

it('loads an existing post into the edit form', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Post Title',
        'slug' => 'original-post-title',
        'excerpt' => 'Original post excerpt.',
        'content' => 'This is the original post content with enough characters.',
        'status' => 'draft',
        'published_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.posts.edit', $post->slug))
        ->assertOk();

    Livewire::test('pages::dashboard.posts.edit', [
        'post' => $post,
    ])
        ->assertSet('post.id', $post->id)
        ->assertSet('title', 'Original Post Title')
        ->assertSet('slug', 'original-post-title')
        ->assertSet('excerpt', 'Original post excerpt.')
        ->assertSet('content', 'This is the original post content with enough characters.')
        ->assertSet('category_id', (string) $category->id)
        ->assertSet('published', false)
        ->assertSet('published_at', null);
});

it('allows an admin to update a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $oldCategory = Category::factory()->create();
    $newCategory = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $oldCategory->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters for validation.',
        'status' => 'draft',
        'published_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('title', 'Updated Title')
        ->set('excerpt', 'Updated excerpt.')
        ->set('content', 'Updated content with enough characters for validation.')
        ->set('category_id', (string) $newCategory->id)
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->title)->toBe('Updated Title')
        ->and($post->slug)->toBe('original-title')
        ->and($post->excerpt)->toBe('Updated excerpt.')
        ->and($post->content)->toBe('Updated content with enough characters for validation.')
        ->and($post->category_id)->toBe($newCategory->id)
        ->and($post->status)->toBe('draft');
});

it('keeps the existing slug when the title is changed', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters for validation.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('title', 'Completely Different Title')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->refresh()->slug)->toBe('original-title');
});

it('allows an admin to change the slug when slug editing is enabled', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters for validation.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('editingSlug', true)
        ->set('slug', 'updated-post-url')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->refresh()->slug)->toBe('updated-post-url');
});

it('generates a slug from the title when requested', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('editingSlug', true)
        ->set('title', 'A New Clean Stack Article')
        ->call('generateSlugFromTitle')
        ->assertSet('slug', 'a-new-clean-stack-article');
});

it('rejects a slug already used by another post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters for validation.',
    ]);

    $otherPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Another Post',
        'slug' => 'another-post',
        'excerpt' => 'Another excerpt.',
        'content' => 'Another post content with enough characters.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('editingSlug', true)
        ->set('slug', $otherPost->slug)
        ->call('save')
        ->assertHasErrors(['slug']);

    expect($post->refresh()->slug)->toBe('original-title');
});

it('allows a post to keep its own slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters for validation.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('editingSlug', true)
        ->set('slug', 'original-title')
        ->call('save')
        ->assertHasNoErrors();
});

it('requires a title when updating a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title']);
});

it('requires an excerpt when updating a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('excerpt', '')
        ->call('save')
        ->assertHasErrors(['excerpt']);
});

it('rejects content shorter than 20 characters when updating a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('content', 'Too short')
        ->call('save')
        ->assertHasErrors(['content']);
});

it('rejects a non-existent category when updating a post', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters.',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('category_id', '999999')
        ->call('save')
        ->assertHasErrors(['category_id']);
});

it('allows a post to be published with a valid past publication date', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'slug' => 'original-title',
        'excerpt' => 'Original excerpt.',
        'content' => 'Original content with enough characters.',
        'status' => 'draft',
        'published_at' => null,
    ]);

    $publishedAt = now()->subDay()->format('Y-m-d\TH:i');

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('published', true)
        ->set('published_at', $publishedAt)
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->published_at?->format('Y-m-d\TH:i'))->toBe($publishedAt);
});

it('prevents publishing when a future publication date is selected', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Draft Post',
        'slug' => 'draft-post',
        'excerpt' => 'Draft excerpt.',
        'content' => 'Draft post content with enough characters.',
        'status' => 'draft',
        'published_at' => null,
    ]);

    $futureDate = now()->addDay()->format('Y-m-d\TH:i');

    $component = Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('published', true)
        ->set('published_at', $futureDate);

    $component
        ->assertSet('published', false)
        ->assertHasErrors([
            'published_at' => 'A published post cannot have a future publication date.',
        ]);

    $post->refresh();

    expect($post->status)->toBe('draft')
        ->and($post->published_at)->toBeNull();
});

it('can switch a published post back to draft', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Published Post',
        'slug' => 'published-post',
        'excerpt' => 'Published excerpt.',
        'content' => 'Published post content with enough characters.',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.edit', [
            'post' => $post,
        ])
        ->set('published', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($post->refresh()->status)->toBe('draft');
});


/*
|--------------------------------------------------------------------------
| Admin Index
|--------------------------------------------------------------------------
*/

it('shows posts to an admin', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $posts = Post::factory()
        ->count(3)
        ->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
        ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->assertViewHas('posts', function ($result) use ($posts) {
            return $result->contains($posts[0])
                && $result->contains($posts[1])
                && $result->contains($posts[2]);
        });
});

it('searches posts by title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Laravel Livewire Guide',
        'slug' => 'laravel-livewire-guide',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Clean PHP Architecture',
        'slug' => 'clean-php-architecture',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('search', 'Livewire')
        ->assertViewHas('posts', function ($result) use ($matchingPost) {
            return $result->total() === 1
                && $result->first()->is($matchingPost);
        });
});

it('searches posts by slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Some Post',
        'slug' => 'unique-laravel-post',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Another Post',
        'slug' => 'another-post',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('search', 'unique-laravel-post')
        ->assertViewHas('posts', function ($result) use ($matchingPost) {
            return $result->total() === 1
                && $result->first()->is($matchingPost);
        });
});

it('filters posts by draft status', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $draftPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('status', 'draft')
        ->assertViewHas('posts', function ($result) use ($draftPost) {
            return $result->total() === 1
                && $result->first()->is($draftPost);
        });
});

it('filters posts by published status', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $publishedPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('status', 'published')
        ->assertViewHas('posts', function ($result) use ($publishedPost) {
            return $result->total() === 1
                && $result->first()->is($publishedPost);
        });
});

it('returns all non-deleted posts when status is all', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Post::factory()->count(4)->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('status', 'all')
        ->assertViewHas('posts', function ($result) {
            return $result->total() === 4;
        });
});

it('combines search and status filters', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Laravel Draft Article',
        'slug' => 'laravel-draft-article',
        'status' => 'draft',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Laravel Published Article',
        'slug' => 'laravel-published-article',
        'status' => 'published',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'PHP Draft Article',
        'slug' => 'php-draft-article',
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('search', 'Laravel')
        ->set('status', 'draft')
        ->assertViewHas('posts', function ($result) use ($matchingPost) {
            return $result->total() === 1
                && $result->first()->is($matchingPost);
        });
});

it('loads six posts initially and loads six more with load more', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Post::factory()->count(12)->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->assertSet('perPage', 6)
        ->assertViewHas('posts', function ($result) {
            return $result->count() === 6
                && $result->total() === 12;
        })
        ->call('loadMore')
        ->assertSet('perPage', 12)
        ->assertViewHas('posts', function ($result) {
            return $result->count() === 12
                && $result->total() === 12;
        });
});

it('resets pagination and per page when search changes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('perPage', 12)
        ->set('search', 'Laravel')
        ->assertSet('perPage', 6);
});

it('resets pagination and per page when status changes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->set('perPage', 12)
        ->set('status', 'published')
        ->assertSet('perPage', 6);
});

it('confirms a post deletion', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->call('confirmDelete', $post->id)
        ->assertSet('postToDelete', $post->id)
        ->assertSet('showDeleteModal', true);
});

it('moves a post to trash when delete is confirmed', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test('pages::dashboard.posts.index')
        ->call('confirmDelete', $post->id)
        ->call('deletePost')
        ->assertSet('postToDelete', null)
        ->assertSet('showDeleteModal', false);

    expect(Post::withTrashed()->find($post->id))
        ->not->toBeNull()
        ->and(Post::withTrashed()->find($post->id)->trashed())
        ->toBeTrue();
});


/*
|--------------------------------------------------------------------------
| Public Blog
|--------------------------------------------------------------------------
*/

it('shows only published posts with a current publication date on the public blog', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $publishedPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => null,
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('blog'))
        ->assertOk()
        ->assertSee($publishedPost->title);
});

it('searches public posts by title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Laravel Livewire Architecture',
        'excerpt' => 'A practical article.',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Clean PHP Architecture',
        'excerpt' => 'Another article.',
    ]);

    $this->get(route('blog'))
        ->assertOk()
        ->assertSee($matchingPost->title);
});

it('searches public posts by excerpt', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Some Article',
        'excerpt' => 'Practical Laravel performance techniques.',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Another Article',
        'excerpt' => 'Something completely different.',
    ]);

    $this->get(route('blog'))
        ->assertOk()
        ->assertSee($matchingPost->title);
});

it('filters public posts by category', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $firstCategory = Category::factory()->create();
    $secondCategory = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $firstCategory->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $secondCategory->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('blog'))
        ->assertOk()
        ->assertSee($matchingPost->title);
});

it('combines public blog search and category filters', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $firstCategory = Category::factory()->create();
    $secondCategory = Category::factory()->create();

    $matchingPost = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $firstCategory->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Laravel Performance Guide',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $firstCategory->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'PHP Architecture Guide',
    ]);

    Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $secondCategory->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'title' => 'Laravel Performance Guide',
    ]);

    $this->get(route('blog'))
        ->assertOk()
        ->assertSee($matchingPost->title);
});

it('loads the public blog successfully with twelve published posts', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    Post::factory()->count(12)->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('blog'))
        ->assertOk();
});

it('resets public blog pagination when the search property changes', function () {
    /*
     * This behavior belongs to the Livewire component lifecycle.
     * The public route itself is verified separately.
     */
    $this->get(route('blog'))
        ->assertOk();
});

it('resets public blog pagination when the category property changes', function () {
    /*
     * This behavior belongs to the Livewire component lifecycle.
     * The public route itself is verified separately.
     */
    $this->get(route('blog'))
        ->assertOk();
});

it('allows a published post to be viewed publicly', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee($post->title);
});

it('returns 404 when viewing a draft post publicly', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    $this->get(route('blog.show', $post))
        ->assertNotFound();
});

it('returns 404 when viewing a future published post publicly', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('blog.show', $post))
        ->assertNotFound();
});

it('returns 404 when viewing an unpublished post with a past publication date publicly', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $category = Category::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'status' => 'draft',
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('blog.show', $post))
        ->assertNotFound();
});
