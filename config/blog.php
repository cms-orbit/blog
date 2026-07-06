<?php

declare(strict_types=1);

return [
    'posts_per_page' => 12,

    'database' => [
        'connection' => env('BLOG_DB_CONNECTION', env('BLOG_DB_DRIVER')),
    ],

    'sso' => [
        'enabled' => true,
    ],
];
