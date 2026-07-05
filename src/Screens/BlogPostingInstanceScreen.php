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

class BlogPostingInstanceScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Posting');
    }

    public function description(): ?string
    {
        return __('Review synced posts, open the public blog, and edit content from the host workspace.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(PostSyncService $sync, string $instanceId): array
    {
        $instance = $this->findBlogInstance($instanceId);
        $source = $sync->resolveSourceInstance();
        $meta = $sync->syncMeta($instance);
        $posts = $sync->listPosts($instance);
        $publicUrl = $this->publicBlogUrl($instance);

        return [
            'instance' => [
                'id' => $instance->getKey(),
                'name' => $instance->name,
                'endpoint' => $instance->primaryEndpoint()?->normalizedValue() ?? '—',
                'publicUrl' => $publicUrl,
                'databaseAvailable' => $sync->instanceDatabaseExists($instance),
            ],
            'source' => $source ? [
                'id' => $source->getKey(),
                'name' => $source->name,
            ] : null,
            'sync' => [
                'last_synced_at' => $meta['last_synced_at'] ?: '—',
            ],
            'postCount' => count($posts),
            'posts' => $posts,
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $instanceId = (string) request()->route('instanceId');
        $instance = $this->findBlogInstance($instanceId);
        $publicUrl = $this->publicBlogUrl($instance);

        $actions = [
            Link::make(__('Back to posting'))
                ->icon('bs.arrow-left')
                ->route('orbit.blog.posting.index'),

            Button::make(__('Sync from catalog'))
                ->icon('bs.arrow-repeat')
                ->method('syncFromCatalog')
                ->confirm(__('Copy all posts from the catalog instance into this workspace?')),
        ];

        if ($publicUrl !== null) {
            $actions[] = Link::make(__('Open blog'))
                ->icon('bs.box-arrow-up-right')
                ->target('_blank')
                ->href($publicUrl);
        }

        return $actions;
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::metrics([
                __('Instance') => 'instance.name',
                __('Endpoint') => 'instance.endpoint',
                __('Last synced') => 'sync.last_synced_at',
                __('Posts') => 'postCount',
            ])->title(__('Workspace')),

            LayoutFactory::table('posts', [
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
                TD::make('publicUrl', __('Public post'))
                    ->render(fn (array $row) => filled($row['publicUrl'] ?? null)
                        ? '<a href="'.e((string) $row['publicUrl']).'" target="_blank" rel="noopener">'.e(__('Open on blog')).'</a>'
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

                        return $links !== [] ? implode(' · ', $links) : '—';
                    }),
            ])->title(__('Posts')),
        ];
    }

    public function syncFromCatalog(PostSyncService $sync): void
    {
        $instanceId = (string) request()->route('instanceId');
        $instance = $this->findBlogInstance($instanceId);

        try {
            $result = $sync->syncToInstance($instance);
            Toast::success(__('Synced :count post(s) into :name.', [
                'count' => $result['synced'],
                'name' => $instance->name,
            ]));
        } catch (\Throwable $exception) {
            Toast::error($exception->getMessage());
        }
    }
}
