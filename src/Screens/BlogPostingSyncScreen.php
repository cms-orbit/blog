<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Services\PostSyncService;
use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Button;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Core\Support\Facades\Toast;
use CmsOrbit\Saas\Instance\Models\Instance;
use Illuminate\Support\Facades\Route;

class BlogPostingSyncScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Posting');
    }

    public function description(): ?string
    {
        return __('Review synced posts across all blog workspaces from one list.');
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
        $source = $sync->resolveSourceInstance();

        $instances = $container
            ? Instance::query()
                ->whereBelongsTo($container, 'container')
                ->with('routeEndpoints')
                ->latest('created_at')
                ->get()
                ->map(function (Instance $instance) use ($sync): array {
                    $meta = $sync->syncMeta($instance);
                    $publicUrl = $this->publicBlogUrl($instance);
                    $instanceId = (string) $instance->getKey();

                    return [
                        'id' => $instanceId,
                        'name' => $instance->name,
                        'endpoint' => $instance->primaryEndpoint()?->normalizedValue() ?? '—',
                        'databaseAvailable' => $sync->instanceDatabaseExists($instance),
                        'lastSyncedAt' => $meta['last_synced_at'],
                        'syncedCount' => $meta['synced_count'],
                        'publicUrl' => $publicUrl,
                        'postsUrl' => Route::has('orbit.blog.posting.instance')
                            ? route('orbit.blog.posting.instance', ['instanceId' => $instanceId])
                            : null,
                    ];
                })
                ->all()
            : [];

        $posts = $container ? $sync->listAllPosts($container) : [];

        return [
            'source' => $source ? [
                'id' => $source->getKey(),
                'name' => $source->name,
                'endpoint' => $source->primaryEndpoint()?->normalizedValue() ?? '—',
            ] : null,
            'instances' => $instances,
            'posts' => $posts,
            'postCount' => count($posts),
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('Blog Hub'))
                ->icon('bs.grid')
                ->route('orbit.blog.index'),

            Button::make(__('Sync all instances'))
                ->icon('bs.arrow-repeat')
                ->method('syncAllInstances')
                ->confirm(__('Copy all posts from the catalog instance into every other blog workspace?')),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::metrics([
                __('Catalog source') => 'source.name',
                __('Source endpoint') => 'source.endpoint',
                __('Posts') => 'postCount',
            ])->title(__('Overview')),

            LayoutFactory::table('posts', [
                TD::make('instanceName', __('Instance'))
                    ->cantHide()
                    ->render(function (array $row): string {
                        $name = e((string) ($row['instanceName'] ?? ''));
                        $path = e((string) ($row['instancePath'] ?? ''));

                        if (filled($row['postsUrl'] ?? null)) {
                            return '<a href="'.e((string) $row['postsUrl'] ?? '').'">'.$name.'</a><br><span class="text-muted small">'.$path.'</span>';
                        }

                        return $name.'<br><span class="text-muted small">'.$path.'</span>';
                    }),
                TD::make('title', __('Title'))
                    ->cantHide()
                    ->render(function (array $row): string {
                        $title = e((string) ($row['title'] ?? ''));

                        if (filled($row['viewUrl'] ?? null)) {
                            return '<a href="'.e((string) $row['viewUrl']).'">'.$title.'</a>';
                        }

                        return $title;
                    }),
                TD::make('statusLabel', __('Status')),
                TD::make('publishedAt', __('Published'))
                    ->render(fn (array $row) => filled($row['publishedAt'] ?? null)
                        ? e((string) $row['publishedAt'])
                        : '—'),
                TD::make('editUrl', __('Actions'))
                    ->render(function (array $row): string {
                        $links = [];

                        if (filled($row['viewUrl'] ?? null)) {
                            $links[] = '<a href="'.e((string) $row['viewUrl']).'">'.e(__('View')).'</a>';
                        }

                        if (filled($row['editUrl'] ?? null)) {
                            $links[] = '<a href="'.e((string) $row['editUrl']).'">'.e(__('Edit')).'</a>';
                        }

                        if (filled($row['publicUrl'] ?? null)) {
                            $links[] = '<a href="'.e((string) $row['publicUrl']).'" target="_blank" rel="noopener">'.e(__('Open on blog')).'</a>';
                        }

                        return $links !== [] ? implode(' · ', $links) : '—';
                    }),
            ])->title(__('Posts')),

            LayoutFactory::table('instances', [
                TD::make('name', __('Instance'))->cantHide(),
                TD::make('endpoint', __('Endpoint')),
                TD::make('lastSyncedAt', __('Last synced'))
                    ->render(fn (array $row) => filled($row['lastSyncedAt'] ?? null)
                        ? e((string) $row['lastSyncedAt'])
                        : '—'),
                TD::make('syncedCount', __('Last sync count'))
                    ->render(fn (array $row) => isset($row['syncedCount'])
                        ? (string) $row['syncedCount']
                        : '—'),
                TD::make('publicUrl', __('Public blog'))
                    ->render(fn (array $row) => filled($row['publicUrl'] ?? null)
                        ? '<a href="'.e((string) $row['publicUrl']).'" target="_blank" rel="noopener">'.e(__('Open blog')).'</a>'
                        : '—'),
            ])->title(__('Workspace sync')),
        ];
    }

    public function syncAllInstances(PostSyncService $sync): void
    {
        $source = $sync->resolveSourceInstance();

        if (! $source instanceof Instance) {
            Toast::error(__('No source blog instance is available for post sync.'));

            return;
        }

        $container = $this->blogContainer();

        if ($container === null) {
            Toast::warning(__('No synced blog container was found yet. Connect the package first, then revisit this workspace.'));

            return;
        }

        $syncedTotal = 0;
        $targetCount = 0;

        $targets = Instance::query()
            ->whereBelongsTo($container, 'container')
            ->whereKeyNot($source->getKey())
            ->get();

        foreach ($targets as $target) {
            if (! $sync->instanceDatabaseExists($target)) {
                continue;
            }

            try {
                $result = $sync->syncToInstance($target, $source);
                $syncedTotal += $result['synced'];
                $targetCount++;
            } catch (\Throwable $exception) {
                Toast::error($exception->getMessage());

                return;
            }
        }

        Toast::success(__('Synced :count post(s) across :targets workspace(s).', [
            'count' => $syncedTotal,
            'targets' => $targetCount,
        ]));
    }
}
