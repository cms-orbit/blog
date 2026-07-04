<?php

declare(strict_types=1);

namespace CmsOrbit\Blog;

use Illuminate\Support\ServiceProvider;

class BlogThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../container/resources/views', 'blog');
    }
}
