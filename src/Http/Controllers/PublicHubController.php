<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Services\PostSyncService;
use CmsOrbit\Blog\Support\BlogContainerDomain;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Models\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class PublicHubController
{
    public function __invoke(PostSyncService $sync): View
    {
        $container = Container::query()->where('slug', 'blog')->first();
        $blogHost = BlogContainerDomain::host();

        $instances = $container instanceof Container
            ? Instance::query()
                ->whereBelongsTo($container, 'container')
                ->with('routeEndpoints')
                ->latest('created_at')
                ->get()
                ->map(function (Instance $instance) use ($sync): array {
                    $endpoint = $instance->primaryEndpoint() ?? $instance->fallbackEndpoint();
                    $counts = $sync->counts($instance);

                    return [
                        'name' => $instance->name,
                        'theme' => $instance->theme ?: 'default',
                        'path' => $endpoint?->normalizedValue() ?? '—',
                        'url' => $endpoint instanceof RouteEndpoint ? $endpoint->canonicalUrl() : null,
                        'publishedPosts' => $counts['published'],
                    ];
                })
                ->filter(fn (array $instance): bool => filled($instance['url']))
                ->values()
                ->all()
            : [];

        return view('blog-package::public.hub', [
            'blogHost' => $blogHost,
            'instances' => $instances,
            'createUrl' => Route::has('orbit.blog.instances.create')
                ? route('orbit.blog.instances.create')
                : null,
        ]);
    }
}
