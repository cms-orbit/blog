<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use Illuminate\Support\Facades\Route;

class BlogInstanceAdminScreen extends Screen
{
    public function name(): ?string
    {
        return __('Blog Admin');
    }

    public function description(): ?string
    {
        return __('Manage posts, categories, tags, and theme settings for this blog instance.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        return [
            'stats' => [
                'posts' => Post::query()->count(),
                'published' => Post::query()->published()->count(),
                'recent' => Post::query()->latest('updated_at')->limit(5)->get(['id', 'title', 'status', 'updated_at']),
            ],
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $links = [];

        if (Route::has('blog.admin.entities.blog-posts.create')) {
            $links[] = Link::make(__('New Post'))
                ->icon('bs.plus-circle')
                ->href(route('blog.admin.entities.blog-posts.create'));
        }

        if (Route::has('blog.admin.theme-settings')) {
            $links[] = Link::make(__('Theme Settings'))
                ->icon('bs.palette')
                ->href(route('blog.admin.theme-settings'));
        }

        return $links;
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::view('blog-package::admin.dashboard'),
        ];
    }
}
