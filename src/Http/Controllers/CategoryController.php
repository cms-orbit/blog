<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use CmsOrbit\Blog\Support\BlogViewResolver;
use Illuminate\Contracts\View\View;

class CategoryController
{
    public function show(string $slug, BlogContainerConfig $config, BlogViewResolver $views): View
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $perPage = (int) $config->themeSetting('posts_per_page', 12);

        $posts = $category->posts()
            ->published()
            ->latest('published_at')
            ->paginate(max(1, $perPage));

        return view($views->resolve('categories.show'), compact('category', 'posts'));
    }
}
