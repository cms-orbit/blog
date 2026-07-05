<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens\Concerns;

use CmsOrbit\Blog\Admin\SignedAdminUrlGenerator;
use CmsOrbit\Saas\Admin\Concerns\FormatsSaasLabels;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Models\Container;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

trait InteractsWithBlogContainer
{
    use FormatsSaasLabels;

    protected function blogContainer(): ?Container
    {
        return Container::query()->where('slug', 'blog')->first();
    }

    /**
     * @return list<string>
     */
    protected function blogInstanceIds(?Container $container): array
    {
        if ($container === null) {
            return [];
        }

        return Instance::query()
            ->whereBelongsTo($container, 'container')
            ->pluck('id')
            ->all();
    }

    protected function blogInstancesUrl(?Container $container): string
    {
        if ($container === null || ! Route::has('orbit.blog.instances.index')) {
            return $this->legacyBlogInstancesUrl($container);
        }

        return route('orbit.blog.instances.index');
    }

    protected function legacyBlogInstancesUrl(?Container $container): string
    {
        if ($container === null || ! Route::has('orbit.entities.instances.index')) {
            return '#';
        }

        return route('orbit.entities.instances.index', [
            'filter' => ['container_id' => $container->getKey()],
        ]);
    }

    protected function blogInstanceCreateUrl(?Container $container): string
    {
        if ($container === null || ! Route::has('orbit.blog.instances.create')) {
            return $this->legacyBlogInstanceCreateUrl($container);
        }

        return route('orbit.blog.instances.create');
    }

    protected function legacyBlogInstanceCreateUrl(?Container $container): string
    {
        if ($container === null || ! Route::has('orbit.entities.instances.create')) {
            return '#';
        }

        return route('orbit.entities.instances.create', [
            'container_id' => $container->getKey(),
        ]);
    }

    protected function containerDetailsUrl(?Container $container): string
    {
        if ($container === null || ! Route::has('orbit.entities.containers.view')) {
            return '#';
        }

        return route('orbit.entities.containers.view', [
            'id' => $container->getKey(),
        ]);
    }

    protected function publicBlogUrl(Instance $instance): ?string
    {
        $endpoint = $instance->primaryEndpoint() ?? $instance->fallbackEndpoint();

        if (! $endpoint instanceof RouteEndpoint) {
            return null;
        }

        return $endpoint->canonicalUrl();
    }

    protected function blogAdminUrl(Instance $instance, ?Authenticatable $user = null): ?string
    {
        $user ??= Auth::guard(config('orbit.guard', 'web'))->user();

        if ($user === null) {
            return null;
        }

        try {
            return app(SignedAdminUrlGenerator::class)->for($instance, $user);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function findBlogInstance(string $id): Instance
    {
        $container = $this->blogContainer();

        abort_if($container === null, 404);

        return Instance::query()
            ->whereBelongsTo($container, 'container')
            ->with(['container', 'routeEndpoints'])
            ->findOrFail($id);
    }
}
