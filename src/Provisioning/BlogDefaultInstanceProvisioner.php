<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Provisioning;

use CmsOrbit\Saas\Container\AutoProvisionDefinition;
use CmsOrbit\Saas\Container\ContainerManager;
use CmsOrbit\Saas\Engine\Events\InstanceCreated;
use CmsOrbit\Saas\Engine\Jobs\CreateInstanceDatabase;
use CmsOrbit\Saas\Engine\Jobs\MigrateInstanceDatabase;
use CmsOrbit\Saas\Engine\Pipeline\JobPipeline;
use CmsOrbit\Saas\Enums\EndpointType;
use CmsOrbit\Saas\Instance\InstanceProvisioner;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Instance\Routing\RouteMapCache;
use CmsOrbit\Saas\Models\Container;
use Illuminate\Support\Facades\DB;

class BlogDefaultInstanceProvisioner
{
    public function __construct(
        protected InstanceProvisioner $provisioner,
        protected RouteMapCache $routeMapCache,
    ) {}

    public function provisionIfNeeded(): ?Instance
    {
        $definition = app(ContainerManager::class)->get('blog');

        if ($definition === null || $definition->autoProvision === null) {
            return null;
        }

        $config = $definition->autoProvision;

        if (! $config->enabled) {
            return null;
        }

        $container = Container::query()->where('slug', 'blog')->first();

        if ($container === null) {
            return null;
        }

        $path = $config->subdomain;

        $existing = $this->findInstanceForPath($container, $path);

        if ($existing !== null) {
            return null;
        }

        $repaired = $this->repairOrphanInstance($container, $config, $path);

        if ($repaired !== null) {
            $this->routeMapCache->build();

            return $repaired;
        }

        if ($this->pathTakenGlobally($path)) {
            return null;
        }

        $instance = $this->provisioner->create(
            container: $container,
            name: $config->name,
            path: $path,
            theme: $config->theme,
        );

        $this->routeMapCache->build();

        return $instance;
    }

    protected function findInstanceForPath(Container $container, string $path): ?Instance
    {
        return Instance::query()
            ->whereBelongsTo($container, 'container')
            ->whereHas('routeEndpoints', fn ($query) => $query
                ->where('type', EndpointType::Path)
                ->where('value', $path))
            ->first();
    }

    protected function repairOrphanInstance(Container $container, AutoProvisionDefinition $config, string $path): ?Instance
    {
        $orphan = Instance::query()
            ->whereBelongsTo($container, 'container')
            ->whereDoesntHave('routeEndpoints', fn ($query) => $query
                ->where('type', EndpointType::Path)
                ->where('value', $path))
            ->where(function ($query) use ($config): void {
                $query->doesntHave('routeEndpoints')
                    ->orWhere('name', $config->name);
            })
            ->orderByRaw('CASE WHEN name = ? THEN 0 ELSE 1 END', [$config->name])
            ->orderBy('created_at')
            ->first();

        if ($orphan === null) {
            return null;
        }

        if ($this->pathTakenGlobally($path)) {
            return null;
        }

        $connection = (string) config('saas.database.host_connection', config('database.default'));

        DB::connection($connection)->transaction(function () use ($orphan, $path): void {
            RouteEndpoint::query()->create([
                'type' => EndpointType::Path,
                'value' => $path,
                'endpointable_type' => $orphan->getMorphClass(),
                'endpointable_id' => $orphan->getKey(),
                'is_primary' => true,
            ]);
        });

        $this->ensureInstanceDatabase($orphan->fresh(['container', 'routeEndpoints']));

        return $orphan->fresh(['container', 'routeEndpoints']);
    }

    protected function ensureInstanceDatabase(Instance $instance): void
    {
        saas()->host(function () use ($instance): void {
            JobPipeline::make([
                CreateInstanceDatabase::class,
                MigrateInstanceDatabase::class,
            ])->send(fn (InstanceCreated $event) => $event->instance)
                ->shouldBeQueued(false)
                ->handle(new InstanceCreated($instance));
        });
    }

    protected function pathTakenGlobally(string $path): bool
    {
        return RouteEndpoint::query()
            ->where('type', EndpointType::Path)
            ->where('value', $path)
            ->exists();
    }
}
