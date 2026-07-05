<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Support;

use CmsOrbit\Saas\Support\ContainerDomain;

class BlogContainerDomain
{
    public static function host(): string
    {
        return ContainerDomain::hostForSlug('blog')
            ?? 'blog.'.(parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost');
    }
}
