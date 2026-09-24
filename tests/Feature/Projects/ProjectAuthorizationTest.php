<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can access the projects index', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.projects.index'))
        ->assertSuccessful();
});

test('regular user cannot access the projects index', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.projects.index'))
        ->assertForbidden();
});

test('guest cannot access the projects index', function () {
    $this->get(route('dashboard.projects.index'))
        ->assertRedirect(route('login'));
});

test('admin can access the project create page', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard.projects.create'))
        ->assertSuccessful();
});

test('regular user cannot access the project create page', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.projects.create'))
        ->assertForbidden();
});

test('guest cannot access the project create page', function () {
    $this->get(route('dashboard.projects.create'))
        ->assertRedirect(route('login'));
});

test('admin can access the project edit page', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard.projects.edit', $project))
        ->assertSuccessful();
});

test('regular user cannot access the project edit page', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.projects.edit', $project))
        ->assertForbidden();
});

test('guest cannot access the project edit page', function () {
    $project = Project::factory()->create();

    $this->get(route('dashboard.projects.edit', $project))
        ->assertRedirect(route('login'));
});
test('admin can create a project', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'A modern business platform built with Laravel and Livewire.')
        ->set('description', '<p>A complete business platform.</p>')
        ->set('live_url', 'https://example.com')
        ->set('github_url', 'https://github.com/example/project')
        ->set('is_featured', true)
        ->set('is_published', true)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $this->assertDatabaseHas('projects', [
        'title' => 'Clean Stack Platform',
        'slug' => 'clean-stack-platform',
        'summary' => 'A modern business platform built with Laravel and Livewire.',
        'description' => '<p>A complete business platform.</p>',
        'live_url' => 'https://example.com',
        'github_url' => 'https://github.com/example/project',
        'is_featured' => true,
        'is_published' => true,
    ]);
});

test('project title automatically generates slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Business Platform')
        ->assertSet('slug', 'clean-stack-business-platform');
});

test('project requires a title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('summary', 'A valid project summary.')
        ->set('description', '<p>A valid project description.</p>')
        ->call('save')
        ->assertHasErrors([
            'title' => 'required',
        ]);
});

test('project requires a summary', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('description', '<p>A valid project description.</p>')
        ->call('save')
        ->assertHasErrors([
            'summary' => 'required',
        ]);
});

test('project requires a description', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'A valid project summary.')
        ->call('save')
        ->assertHasErrors([
            'description' => 'required',
        ]);
});

test('project accepts empty optional url fields', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'A valid project summary.')
        ->set('description', '<p>A valid project description.</p>')
        ->set('live_url', '')
        ->set('github_url', '')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'title' => 'Clean Stack Platform',
        'live_url' => null,
        'github_url' => null,
    ]);
});

test('project rejects an invalid live url', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'A valid project summary.')
        ->set('description', '<p>A valid project description.</p>')
        ->set('live_url', 'not-a-url')
        ->call('save')
        ->assertHasErrors([
            'live_url' => 'url',
        ]);
});

test('project rejects an invalid github url', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'A valid project summary.')
        ->set('description', '<p>A valid project description.</p>')
        ->set('github_url', 'not-a-url')
        ->call('save')
        ->assertHasErrors([
            'github_url' => 'url',
        ]);
});

test('project uses false as the default value for featured and published', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->assertSet('is_featured', false)
        ->assertSet('is_published', false);
});

test('project can be created as featured and published', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Featured Clean Stack Project')
        ->set('summary', 'A featured project summary.')
        ->set('description', '<p>A featured project.</p>')
        ->set('is_featured', true)
        ->set('is_published', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'title' => 'Featured Clean Stack Project',
        'is_featured' => true,
        'is_published' => true,
    ]);
});

test('project slug must be unique', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'slug' => 'clean-stack-platform',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Clean Stack Platform')
        ->set('summary', 'Another project summary.')
        ->set('description', '<p>Another project.</p>')
        ->call('save')
        ->assertHasErrors([
            'slug' => 'unique',
        ]);
});
test('admin can update a project', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'title' => 'Original Project',
        'slug' => 'original-project',
        'summary' => 'Original summary.',
        'description' => '<p>Original description.</p>',
        'live_url' => 'https://example.com/original',
        'github_url' => 'https://github.com/example/original',
        'is_featured' => false,
        'is_published' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('title', 'Updated Project')
        ->set('summary', 'Updated project summary.')
        ->set('description', '<p>Updated project description.</p>')
        ->set('live_url', 'https://example.com/updated')
        ->set('github_url', 'https://github.com/example/updated')
        ->set('is_featured', true)
        ->set('is_published', true)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => 'Updated Project',
        'slug' => 'original-project',
        'summary' => 'Updated project summary.',
        'description' => '<p>Updated project description.</p>',
        'live_url' => 'https://example.com/updated',
        'github_url' => 'https://github.com/example/updated',
        'is_featured' => true,
        'is_published' => true,
    ]);
});

test('editing a project keeps the existing slug when slug editing is disabled', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'title' => 'Original Project',
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('title', 'Completely Different Project')
        ->assertSet('slug', 'original-project');
});

test('admin can change a project slug when slug editing is enabled', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'title' => 'Original Project',
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('editingSlug', true)
        ->set('slug', 'updated-project')
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'slug' => 'updated-project',
    ]);
});

test('project slug must be unique when editing another project', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $existingProject = Project::factory()->create([
        'slug' => 'existing-project',
    ]);

    $project = Project::factory()->create([
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('editingSlug', true)
        ->set('slug', $existingProject->slug)
        ->call('save')
        ->assertHasErrors([
            'slug' => 'unique',
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'slug' => 'original-project',
    ]);
});

test('project can keep its own slug when editing', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('editingSlug', true)
        ->set('slug', 'original-project')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'slug' => 'original-project',
    ]);
});

test('project edit requires a title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors([
            'title' => 'required',
        ]);
});

test('project edit requires a summary', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('summary', '')
        ->call('save')
        ->assertHasErrors([
            'summary' => 'required',
        ]);
});

test('project edit requires a description', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('description', '')
        ->call('save')
        ->assertHasErrors([
            'description' => 'required',
        ]);
});

test('project edit rejects an invalid live url', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('live_url', 'not-a-url')
        ->call('save')
        ->assertHasErrors([
            'live_url' => 'url',
        ]);
});

test('project edit rejects an invalid github url', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('github_url', 'not-a-url')
        ->call('save')
        ->assertHasErrors([
            'github_url' => 'url',
        ]);
});

test('project edit accepts empty optional url fields', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'live_url' => 'https://example.com',
        'github_url' => 'https://github.com/example/project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('live_url', '')
        ->set('github_url', '')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'live_url' => null,
        'github_url' => null,
    ]);
});

test('project edit can update featured and published states', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'is_featured' => false,
        'is_published' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('is_featured', true)
        ->set('is_published', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'is_featured' => true,
        'is_published' => true,
    ]);
});
test('admin can delete a project', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create();

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->call('confirmDelete', $project->id)
        ->assertSet('projectToDelete', $project->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteProject')
        ->assertSet('projectToDelete', null)
        ->assertSet('showDeleteModal', false);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('admin can delete a project without a cover image', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'cover_image' => null,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->call('confirmDelete', $project->id)
        ->call('deleteProject');

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('admin can delete a project with a cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/test-project.jpg',
        'fake-image-content'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/test-project.jpg',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->call('confirmDelete', $project->id)
        ->call('deleteProject');

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);

    Storage::disk('public')->assertMissing(
        'projects/test-project.jpg'
    );
});

test('public projects page is accessible to guests', function () {
    $this->get(route('projects.index'))
        ->assertSuccessful();
});

test('public projects page only displays published projects', function () {
    $publishedProject = Project::factory()->create([
        'title' => 'Published Project',
        'slug' => 'published-project',
        'is_published' => true,
    ]);

    Project::factory()->create([
        'title' => 'Unpublished Project',
        'slug' => 'unpublished-project',
        'is_published' => false,
    ]);

    $response = $this->get(route('projects.index'));

    $response
        ->assertSuccessful()
        ->assertSee('Published Project')
        ->assertDontSee('Unpublished Project');
});

test('published project appears on the public projects page', function () {
    Project::factory()->create([
        'title' => 'Public Clean Stack Project',
        'slug' => 'public-clean-stack-project',
        'summary' => 'A public project summary.',
        'is_published' => true,
    ]);

    $this->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Public Clean Stack Project')
        ->assertSee('A public project summary.');
});

test('unpublished project does not appear on the public projects page', function () {
    Project::factory()->create([
        'title' => 'Private Clean Stack Project',
        'slug' => 'private-clean-stack-project',
        'is_published' => false,
    ]);

    $this->get(route('projects.index'))
        ->assertSuccessful()
        ->assertDontSee('Private Clean Stack Project');
});
test('projects index can search by title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Laravel Business Platform',
        'slug' => 'laravel-business-platform',
    ]);

    Project::factory()->create([
        'title' => 'Portfolio Website',
        'slug' => 'portfolio-website',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('search', 'Laravel Business')
        ->assertSee('Laravel Business Platform')
        ->assertDontSee('Portfolio Website');
});

test('projects index can search by slug', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Business Platform',
        'slug' => 'clean-stack-business-platform',
    ]);

    Project::factory()->create([
        'title' => 'Portfolio Website',
        'slug' => 'portfolio-website',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('search', 'clean-stack-business')
        ->assertSee('Business Platform')
        ->assertDontSee('Portfolio Website');
});

test('projects index can filter published projects', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Published Project',
        'is_published' => true,
    ]);

    Project::factory()->create([
        'title' => 'Unpublished Project',
        'is_published' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('status', 'published')
        ->assertSee('Published Project')
        ->assertDontSee('Unpublished Project');
});

test('projects index can filter unpublished projects', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Published Project',
        'is_published' => true,
    ]);

    Project::factory()->create([
        'title' => 'Unpublished Project',
        'is_published' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('status', 'unpublished')
        ->assertSee('Unpublished Project')
        ->assertDontSee('Published Project');
});

test('projects index can filter featured projects', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Featured Project',
        'is_featured' => true,
    ]);

    Project::factory()->create([
        'title' => 'Regular Project',
        'is_featured' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('featured', 'featured')
        ->assertSee('Featured Project')
        ->assertDontSee('Regular Project');
});

test('projects index can filter not featured projects', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Project::factory()->create([
        'title' => 'Featured Project',
        'is_featured' => true,
    ]);

    Project::factory()->create([
        'title' => 'Regular Project',
        'is_featured' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('featured', 'not_featured')
        ->assertSee('Regular Project')
        ->assertDontSee('Featured Project');
});

test('projects index resets pagination when search changes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('perPage', 12)
        ->set('search', 'Laravel')
        ->assertSet('perPage', 6);
});

test('projects index resets pagination when status changes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('perPage', 12)
        ->set('status', 'published')
        ->assertSet('perPage', 6);
});

test('projects index resets pagination when featured filter changes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->set('perPage', 12)
        ->set('featured', 'featured')
        ->assertSet('perPage', 6);
});

test('projects index load more increases the number of projects per page', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->assertSet('perPage', 6)
        ->call('loadMore')
        ->assertSet('perPage', 12)
        ->call('loadMore')
        ->assertSet('perPage', 18);
});

test('create project slug can be generated from the title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'My New Clean Stack Project')
        ->assertSet('slug', 'my-new-clean-stack-project');
});

test('edit project can generate a slug from the title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'title' => 'Original Project',
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->call('generateSlugFromTitle')
        ->assertSet('slug', 'original-project');
});

test('edit project can generate a new slug from an updated title', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $project = Project::factory()->create([
        'title' => 'Original Project',
        'slug' => 'original-project',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('title', 'Updated Clean Stack Project')
        ->call('generateSlugFromTitle')
        ->assertSet('slug', 'updated-clean-stack-project');
});
test('admin can create a project with a cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $image = UploadedFile::fake()->image(
        'cover.jpg',
        1200,
        630
    );

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Project With Cover')
        ->set('summary', 'Project summary.')
        ->set('description', '<p>Project description.</p>')
        ->set('coverImage', $image)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $project = Project::query()
        ->where('title', 'Project With Cover')
        ->firstOrFail();

    expect($project->cover_image)->not->toBeNull();

    Storage::disk('public')->assertExists($project->cover_image);
});

test('create project rejects an invalid cover image type', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $file = UploadedFile::fake()->create(
        'document.pdf',
        100,
        'application/pdf'
    );

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Invalid Image Project')
        ->set('summary', 'Project summary.')
        ->set('description', '<p>Project description.</p>')
        ->set('coverImage', $file)
        ->call('save')
        ->assertHasErrors([
            'coverImage',
        ]);

    $this->assertDatabaseMissing('projects', [
        'title' => 'Invalid Image Project',
    ]);
});

test('create project rejects a cover image with invalid dimensions', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $image = UploadedFile::fake()->image(
        'cover.jpg',
        800,
        400
    );

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.create')
        ->set('title', 'Small Image Project')
        ->set('summary', 'Project summary.')
        ->set('description', '<p>Project description.</p>')
        ->set('coverImage', $image)
        ->call('save')
        ->assertHasErrors([
            'coverImage',
        ]);

    $this->assertDatabaseMissing('projects', [
        'title' => 'Small Image Project',
    ]);
});

test('admin can replace an existing project cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/old-cover.jpg',
        'old image'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/old-cover.jpg',
    ]);

    $newImage = UploadedFile::fake()->image(
        'new-cover.jpg',
        1200,
        630
    );

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->set('coverImage', $newImage)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $project->refresh();

    expect($project->cover_image)
        ->not->toBeNull()
        ->and($project->cover_image)
        ->not->toBe('projects/old-cover.jpg');

    Storage::disk('public')->assertExists($project->cover_image);
    Storage::disk('public')->assertMissing('projects/old-cover.jpg');
});

test('admin can remove an existing project cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/existing-cover.jpg',
        'existing image'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/existing-cover.jpg',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->call('removeCoverImage')
        ->assertSet('removeExistingCoverImage', true)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $project->refresh();

    expect($project->cover_image)->toBeNull();

    Storage::disk('public')->assertMissing(
        'projects/existing-cover.jpg'
    );
});

test('admin can restore an existing project cover image before saving', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/existing-cover.jpg',
        'existing image'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/existing-cover.jpg',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->call('removeCoverImage')
        ->assertSet('removeExistingCoverImage', true)
        ->call('restoreExistingCoverImage')
        ->assertSet('removeExistingCoverImage', false)
        ->call('save')
        ->assertRedirect(route('dashboard.projects.index'));

    $project->refresh();

    expect($project->cover_image)
        ->toBe('projects/existing-cover.jpg');

    Storage::disk('public')->assertExists(
        'projects/existing-cover.jpg'
    );
});

test('uploading a new project cover image cancels removal of the existing image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/existing-cover.jpg',
        'existing image'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/existing-cover.jpg',
    ]);

    $newImage = UploadedFile::fake()->image(
        'new-cover.jpg',
        1200,
        630
    );

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.edit', [
        'project' => $project,
    ])
        ->call('removeCoverImage')
        ->assertSet('removeExistingCoverImage', true)
        ->set('coverImage', $newImage)
        ->assertSet('removeExistingCoverImage', false);
});

test('admin can delete a project with its cover image', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Storage::disk('public')->put(
        'projects/delete-cover.jpg',
        'cover image'
    );

    $project = Project::factory()->create([
        'cover_image' => 'projects/delete-cover.jpg',
    ]);

    $this->actingAs($admin);

    Livewire::test('pages::dashboard.projects.index')
        ->call('confirmDelete', $project->id)
        ->assertSet('projectToDelete', $project->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteProject');

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);

    Storage::disk('public')->assertMissing(
        'projects/delete-cover.jpg'
    );
});
