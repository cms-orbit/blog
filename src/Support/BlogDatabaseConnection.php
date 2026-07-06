<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Support;

use CmsOrbit\Saas\Support\HostConnection;

class BlogDatabaseConnection
{
    public static function name(): string
    {
        $connection = config('blog.database.connection');

        if (is_string($connection) && $connection !== '') {
            return $connection;
        }

        return HostConnection::name();
    }
}
