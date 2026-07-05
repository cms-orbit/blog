<?php

declare(strict_types=1);

use CmsOrbit\Blog\Screens\BlogHubScreen;
use CmsOrbit\Blog\Screens\BlogInstanceCreateScreen;
use CmsOrbit\Blog\Screens\BlogInstanceListScreen;
use CmsOrbit\Blog\Screens\BlogInstanceViewScreen;
use CmsOrbit\Blog\Screens\BlogPostingEditScreen;
use CmsOrbit\Blog\Screens\BlogPostingInstanceScreen;
use CmsOrbit\Blog\Screens\BlogPostingSyncScreen;
use CmsOrbit\Blog\Screens\BlogPostingViewScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('blog', BlogHubScreen::class)
    ->name('blog.index')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('orbit.index')
        ->push(__('Blog Hub')));

Route::screen('blog/instances', BlogInstanceListScreen::class)
    ->name('blog.instances.index')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('orbit.blog.index')
        ->push(__('Blog Instances')));

Route::screen('blog/instances/create', BlogInstanceCreateScreen::class)
    ->name('blog.instances.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('orbit.blog.instances.index')
        ->push(__('Create Blog Instance')));

Route::screen('blog/instances/{id}', BlogInstanceViewScreen::class)
    ->name('blog.instances.view')
    ->breadcrumbs(fn (Trail $trail, string $id) => $trail
        ->parent('orbit.blog.instances.index')
        ->push($id));

Route::screen('blog/posting', BlogPostingSyncScreen::class)
    ->name('blog.posting.index')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('orbit.blog.index')
        ->push(__('Posting')));

Route::screen('blog/posting/{instanceId}', BlogPostingInstanceScreen::class)
    ->name('blog.posting.instance')
    ->breadcrumbs(fn (Trail $trail, string $instanceId) => $trail
        ->parent('orbit.blog.posting.index')
        ->push($instanceId));

Route::screen('blog/posting/{instanceId}/posts/{postId}', BlogPostingViewScreen::class)
    ->name('blog.posting.posts.view')
    ->breadcrumbs(fn (Trail $trail, string $instanceId, int $postId) => $trail
        ->parent('orbit.blog.posting.index')
        ->push($instanceId)
        ->push((string) $postId));

Route::screen('blog/posting/{instanceId}/posts/{postId}/edit', BlogPostingEditScreen::class)
    ->name('blog.posting.posts.edit')
    ->breadcrumbs(fn (Trail $trail, string $instanceId, int $postId) => $trail
        ->parent('orbit.blog.posting.index')
        ->push($instanceId)
        ->push((string) $postId)
        ->push(__('Edit')));
