<?php

declare(strict_types=1);

use CmsOrbit\Blog\Screens\BlogHubScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('blog', BlogHubScreen::class)
    ->name('blog.index')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('orbit.index')
        ->push(__('Blog Hub')));
