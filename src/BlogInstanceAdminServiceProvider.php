<?php

declare(strict_types=1);

namespace CmsOrbit\Blog;

use CmsOrbit\Blog\Admin\BlogInstanceAdminRouteRegistrar;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use Illuminate\Support\ServiceProvider;

class BlogInstanceAdminServiceProvider extends ServiceProvider
{
    public function boot(BlogInstanceAdminRouteRegistrar $registrar, BlogContainerConfig $config): void
    {
        $context = instance_context();

        if ($context === null || $context->container->slug !== 'blog') {
            return;
        }

        $registrar->register($config->adminPrefix());
    }
}
