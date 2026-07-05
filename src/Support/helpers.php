<?php

declare(strict_types=1);

use CmsOrbit\Blog\Support\BlogContainerConfig;

if (! function_exists('blog_theme')) {
    function blog_theme(string $key, mixed $default = null): mixed
    {
        return app(BlogContainerConfig::class)->themeSetting($key, $default);
    }
}

if (! function_exists('blog_admin_prefix')) {
    function blog_admin_prefix(): string
    {
        return app(BlogContainerConfig::class)->adminPrefix();
    }
}
