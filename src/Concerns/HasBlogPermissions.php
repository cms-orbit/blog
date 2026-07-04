<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Concerns;

trait HasBlogPermissions
{
    public function permissionKey(): string
    {
        return 'blog.entities.'.static::uriKey();
    }
}
