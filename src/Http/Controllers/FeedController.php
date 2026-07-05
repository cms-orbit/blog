<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Blog\Support\BlogViewResolver;
use Illuminate\Http\Response;

class FeedController
{
    public function __invoke(BlogViewResolver $views): Response
    {
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->limit(20)
            ->get();

        $xml = view($views->resolve('feed.rss'), compact('posts'))->render();

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
