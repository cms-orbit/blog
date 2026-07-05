<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use CmsOrbit\Blog\Support\BlogViewResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController
{
    public function index(Request $request, BlogContainerConfig $config, BlogViewResolver $views): View
    {
        $perPage = (int) $config->themeSetting('posts_per_page', 12);

        $posts = Post::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->paginate(max(1, $perPage));

        return view($views->resolve('posts.index'), compact('posts'));
    }

    public function show(string $slug, BlogViewResolver $views): View
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view($views->resolve('posts.show'), compact('post'));
    }
}
