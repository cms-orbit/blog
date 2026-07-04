<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Saas\Admin\Concerns\FormatsSaasLabels;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Models\Container;
use CmsOrbit\Saas\Theme\ThemeRegistry;
use Illuminate\Support\Facades\Route;

class BlogHubScreen extends Screen
{
    use FormatsSaasLabels;

    public function name(): ?string
    {
        return __('Blog Hub');
    }

    public function description(): ?string
    {
        return __('Manage the blog container, instances, themes, and routing from one place.');
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
        $container = $this->blogContainer();
        $instanceIds = $this->blogInstanceIds($container);

        return [
            'hub' => [
                'container' => $container ? [
                    'name' => $this->containerNameLabel($container->name),
                    'slug' => $container->slug,
                    'isolationLabel' => $this->isolationLabel($container->isolation_engine),
                    'lifecycleLabel' => $this->lifecycleLabel($container->lifecycle),
                    'routingSupports' => $this->routingSupportLabels($container->routing_supports ?? []),
                    'themeSelectable' => $container->theme_selectable,
                ] : null,
                'metrics' => [
                    'instances' => count($instanceIds),
                    'activeInstances' => $container
                        ? Instance::query()
                            ->whereBelongsTo($container, 'container')
                            ->where('lifecycle', 'active')
                            ->count()
                        : 0,
                    'endpoints' => empty($instanceIds)
                        ? 0
                        : RouteEndpoint::query()
                            ->where('endpointable_type', Instance::class)
                            ->whereIn('endpointable_id', $instanceIds)
                            ->count(),
                    'themes' => count(app(ThemeRegistry::class)->forContainer('blog')),
                ],
                'links' => [
                    [
                        'title' => __('Blog Instances'),
                        'description' => __('Review connected blog instances, lifecycle states, and primary endpoints.'),
                        'url' => $this->blogInstancesUrl($container),
                        'cta' => __('Open instances'),
                    ],
                    [
                        'title' => __('Create Blog Instance'),
                        'description' => __('Provision a new blog instance with the blog container preselected.'),
                        'url' => $this->blogInstanceCreateUrl($container),
                        'cta' => __('Create instance'),
                    ],
                    [
                        'title' => __('Container Details'),
                        'description' => __('Inspect container capabilities, routing support, and theme options.'),
                        'url' => $this->containerDetailsUrl($container),
                        'cta' => __('Inspect container'),
                    ],
                ],
                'instances' => $container
                    ? Instance::query()
                        ->whereBelongsTo($container, 'container')
                        ->with('routeEndpoints')
                        ->latest('created_at')
                        ->limit(6)
                        ->get()
                        ->map(fn (Instance $instance) => [
                            'name' => $instance->name,
                            'lifecycleLabel' => $this->lifecycleLabel($instance->lifecycle),
                            'theme' => $instance->theme ?: __('Default'),
                            'primaryEndpoint' => $instance->primaryEndpoint()?->normalizedValue() ?? '—',
                            'url' => Route::has('orbit.entities.instances.view')
                                ? route('orbit.entities.instances.view', ['id' => $instance->getKey()])
                                : null,
                        ])
                        ->all()
                    : [],
                'endpoints' => empty($instanceIds)
                    ? []
                    : RouteEndpoint::query()
                        ->with('endpointable')
                        ->where('endpointable_type', Instance::class)
                        ->whereIn('endpointable_id', $instanceIds)
                        ->latest('created_at')
                        ->limit(8)
                        ->get()
                        ->map(fn (RouteEndpoint $endpoint) => [
                            'instanceName' => $endpoint->endpointable?->getAttribute('name') ?: '—',
                            'typeLabel' => $this->routingSupportLabel($endpoint->type->value),
                            'value' => $endpoint->normalizedValue(),
                            'primary' => $endpoint->is_primary,
                            'fallback' => $endpoint->is_fallback,
                        ])
                        ->all(),
            ],
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $container = $this->blogContainer();

        return [
            Link::make(__('Blog Instances'))
                ->icon('bs.collection')
                ->href($this->blogInstancesUrl($container)),

            Link::make(__('Create Blog Instance'))
                ->icon('bs.plus-circle')
                ->href($this->blogInstanceCreateUrl($container)),

            Link::make(__('Container Details'))
                ->icon('bs.box-seam')
                ->href($this->containerDetailsUrl($container)),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::view('blog::hub'),
        ];
    }

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
        if ($container === null || ! Route::has('orbit.entities.instances.index')) {
            return '#';
        }

        return route('orbit.entities.instances.index', [
            'filter' => ['container_id' => $container->getKey()],
        ]);
    }

    protected function blogInstanceCreateUrl(?Container $container): string
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
}
