<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Services\PostSyncService;
use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Saas\Instance\Models\Instance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class BlogInstanceListScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Blog Instances');
    }

    public function description(): ?string
    {
        return __('Review blog workspaces, owners, public URLs, and instance admin access.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(PostSyncService $sync): array
    {
        $container = $this->blogContainer();
        $user = Auth::guard(config('orbit.guard', 'web'))->user();
        $totalPosts = 0;
        $publishedPosts = 0;

        $instances = $container
            ? Instance::query()
                ->whereBelongsTo($container, 'container')
                ->with('routeEndpoints')
                ->latest('created_at')
                ->get()
                ->map(function (Instance $instance) use ($sync, $user, &$totalPosts, &$publishedPosts): array {
                    $counts = $sync->counts($instance);
                    $totalPosts += $counts['total'];
                    $publishedPosts += $counts['published'];
                    $publicUrl = $this->publicBlogUrl($instance);
                    $adminUrl = $user !== null ? $this->blogAdminUrl($instance, $user) : null;

                    return [
                        'id' => $instance->getKey(),
                        'name' => $instance->name,
                        'email' => $instance->email ?: '—',
                        'lifecycleLabel' => $this->lifecycleLabel($instance->lifecycle),
                        'theme' => $instance->theme ?: __('Default'),
                        'primaryEndpoint' => $instance->primaryEndpoint()?->normalizedValue() ?? '—',
                        'totalPosts' => $counts['total'],
                        'publishedPosts' => $counts['published'],
                        'databaseAvailable' => $sync->instanceDatabaseExists($instance),
                        'publicUrl' => $publicUrl,
                        'adminUrl' => $adminUrl,
                        'viewUrl' => Route::has('orbit.blog.instances.view')
                            ? route('orbit.blog.instances.view', ['id' => $instance->getKey()])
                            : null,
                    ];
                })
                ->all()
            : [];

        return [
            'container' => $container ? [
                'name' => $this->containerNameLabel($container->name),
                'slug' => $container->slug,
            ] : null,
            'metrics' => [
                'totalPosts' => $totalPosts,
                'publishedPosts' => $publishedPosts,
            ],
            'instances' => $instances,
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $container = $this->blogContainer();

        return [
            Link::make(__('Blog Hub'))
                ->icon('bs.grid')
                ->route('orbit.blog.index'),

            Link::make(__('Create Blog Instance'))
                ->icon('bs.plus-circle')
                ->href($this->blogInstanceCreateUrl($container))
                ->canSee($container !== null),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::metrics([
                __('Total posts') => 'metrics.totalPosts',
                __('Published posts') => 'metrics.publishedPosts',
            ])->title(__('Post totals')),

            LayoutFactory::table('instances', [
                TD::make('name', __('Name'))
                    ->cantHide()
                    ->render(function (array $row): string {
                        $name = e((string) $row['name']);

                        if (filled($row['viewUrl'] ?? null)) {
                            return '<a href="'.e((string) $row['viewUrl']).'">'.$name.'</a>';
                        }

                        return $name;
                    }),
                TD::make('totalPosts', __('Total posts')),
                TD::make('publishedPosts', __('Published posts')),
                TD::make('email', __('Owner')),
                TD::make('lifecycleLabel', __('Lifecycle')),
                TD::make('theme', __('Theme')),
                TD::make('primaryEndpoint', __('Primary Endpoint')),
                TD::make('publicUrl', __('Public Site'))
                    ->render(fn (array $row) => filled($row['publicUrl'] ?? null)
                        ? '<a href="'.e((string) $row['publicUrl']).'" target="_blank" rel="noopener">'.e(__('Open blog')).'</a>'
                        : '—'),
                TD::make('adminUrl', __('Admin'))
                    ->render(fn (array $row) => filled($row['adminUrl'] ?? null)
                        ? '<a href="'.e((string) $row['adminUrl']).'" target="_blank" rel="noopener">'.e(__('Manage blog')).'</a>'
                        : '—'),
            ])->title(__('Blog Instances')),
        ];
    }
}
