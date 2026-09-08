<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Database\Seeders;

use CmsOrbit\Blog\Database\Factories\CategoryFactory;
use CmsOrbit\Blog\Database\Factories\PostFactory;
use CmsOrbit\Blog\Models\BlogSetting;
use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use CmsOrbit\Saas\Container\Manifest\ContainerMapCache;
use CmsOrbit\Saas\Enums\EndpointType;
use CmsOrbit\Saas\Instance\InstanceProvisioner;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Instance\Routing\RouteMapCache;
use CmsOrbit\Saas\Models\Container;
use CmsOrbit\Saas\Support\HostConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoBlogInstancesSeeder extends Seeder
{
    /** @var list<array{name: string, path: string, theme: string}> */
    protected array $demos = [
        ['name' => 'Tech Pulse', 'path' => 'tech', 'theme' => 'minimal'],
        ['name' => 'Food Stories', 'path' => 'food', 'theme' => 'editorial'],
        ['name' => 'Travel Notes', 'path' => 'travel', 'theme' => 'magazine'],
        ['name' => 'Night Mode', 'path' => 'night', 'theme' => 'dark'],
        ['name' => 'Classic Journal', 'path' => 'journal', 'theme' => 'classic'],
        ['name' => 'Photo Walk', 'path' => 'photo', 'theme' => 'photo'],
        ['name' => 'Corp Insights', 'path' => 'corp', 'theme' => 'corporate'],
        ['name' => 'Playground', 'path' => 'play', 'theme' => 'playful'],
        ['name' => 'Neon City', 'path' => 'neon', 'theme' => 'neon'],
        ['name' => 'Dev Diary', 'path' => 'dev', 'theme' => 'minimal'],
    ];

    public function run(): void
    {
        $registrar = app(HostContainerAdminRegistrar::class);
        $registrar->registerProviders();
        $registrar->syncContainers();

        app(ContainerMapCache::class)->build();

        $container = Container::query()->where('slug', 'blog')->first();

        if ($container === null) {
            return;
        }

        $provisioner = app(InstanceProvisioner::class);

        foreach ($this->demos as $demo) {
            if ($this->pathTaken($demo['path'])) {
                continue;
            }

            $instance = $provisioner->create(
                container: $container,
                name: $demo['name'],
                path: $demo['path'],
                theme: $demo['theme'],
            );

            $this->seedInstanceContent($instance);
        }

        app(RouteMapCache::class)->build();
    }

    protected function seedInstanceContent(Instance $instance): void
    {
        saas()->run($instance, function (): void {
            if (Post::query()->exists()) {
                return;
            }

            $categories = CategoryFactory::new()->count(random_int(2, 5))->create();

            PostFactory::new()
                ->count(random_int(10, 30))
                ->published()
                ->recycle($categories)
                ->create();

            BlogSetting::setValue('theme.primary_color', fake()->hexColor());
            BlogSetting::setValue('theme.posts_per_page', random_int(6, 18));
        });

        saas()->end();
        DB::purge('instance');
        DB::setDefaultConnection(HostConnection::name());
    }

    protected function pathTaken(string $path): bool
    {
        return RouteEndpoint::query()
            ->where('type', EndpointType::Path)
            ->where('value', $path)
            ->exists();
    }
}
