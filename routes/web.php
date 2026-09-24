<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/blog', 'pages::blog')->name('blog');
Route::livewire('/blog/{post}', 'pages::blog-post')->name('blog.show');
Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/services', 'pages::services.index')->name('services.index');
Route::livewire('/services/{service}', 'pages::services.show')->name('services.show');
Route::livewire('/projects', 'pages::projects.index')->name('projects.index');
Route::livewire('/about', 'pages::about')->name('about');
Route::livewire('/contact', 'pages::contact')->name('contact');
Route::livewire('/lets-talk', 'pages::lets-talk')->name('lets-talk');

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
});

Route::prefix('dashboard')->middleware(['auth', 'verified','admin'])->group(function () {
    Route::livewire('/leads', 'pages::dashboard.leads')->name('dashboard.leads');
    Route::livewire('/leads/{lead}', 'pages::dashboard.lead')->name('dashboard.leads.show');
    Route::livewire('/posts', 'pages::dashboard.posts.index')->name('dashboard.posts.index');
    Route::livewire('/posts/create', 'pages::dashboard.posts.create')->name('dashboard.posts.create');
    Route::livewire('/posts/{post}/edit', 'pages::dashboard.posts.edit')->name('dashboard.posts.edit');
    Route::livewire('/posts/trash', 'pages::dashboard.posts.trash')->name('dashboard.posts.trash');
    Route::livewire('/categories', 'pages::dashboard.categories.index')->name('dashboard.categories.index');
    Route::livewire('/categories/create', 'pages::dashboard.categories.create')->name('dashboard.categories.create');
    Route::livewire('/categories/{category}/edit', 'pages::dashboard.categories.edit')->name('dashboard.categories.edit');
    Route::livewire('/services', 'pages::dashboard.services.index')->name('dashboard.services.index');
    Route::livewire('/services/create', 'pages::dashboard.services.create')->name('dashboard.services.create');
    Route::livewire('/services/{service}/edit', 'pages::dashboard.services.edit')->name('dashboard.services.edit');
    Route::livewire('/projects', 'pages::dashboard.projects.index')->name('dashboard.projects.index');
    Route::livewire('/projects/create', 'pages::dashboard.projects.create')->name('dashboard.projects.create');
    Route::livewire('/projects/{project}/edit', 'pages::dashboard.projects.edit')->name('dashboard.projects.edit');
    Route::livewire('/site-pages', 'pages::dashboard.site-pages.index')->name('dashboard.site-pages.index');
    Route::livewire('/site-pages/home/edit', 'pages::dashboard.site-pages.home.edit')->name('dashboard.site-pages.home.edit');
    Route::livewire('/site-pages/about/edit', 'pages::dashboard.site-pages.about.edit')->name('dashboard.site-pages.about.edit');
    Route::livewire('/site-pages/contact/edit', 'pages::dashboard.site-pages.contact.edit')->name('dashboard.site-pages.contact.edit');
    Route::livewire('/site-pages/lets-talk/edit', 'pages::dashboard.site-pages.lets-talk.edit')->name('dashboard.site-pages.lets-talk.edit');
});

require __DIR__.'/settings.php';
