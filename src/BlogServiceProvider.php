<?php

declare(strict_types=1);

namespace CmsOrbit\Blog;

use CmsOrbit\Blog\Entities\PostEntity;
use CmsOrbit\Core\Foundation\Entity\EntityRegistry;
use CmsOrbit\Core\Foundation\OrbitServiceProvider;
use CmsOrbit\Saas\Theme\Theme;

class BlogServiceProvider extends OrbitServiceProvider
{
    public function register(): void
    {
        $this->app->afterResolving(EntityRegistry::class, function (EntityRegistry $registry): void {
            $registry->registerClass([PostEntity::class]);
        });

        if ($this->app->resolved(EntityRegistry::class)) {
            $this->app->make(EntityRegistry::class)->registerClass([PostEntity::class]);
        }
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Theme::register('blog', 'default', BlogThemeServiceProvider::class);
    }
}
