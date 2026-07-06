<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Services;

use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Blog\Support\BlogDatabaseConnection;
use CmsOrbit\Saas\Instance\Models\Instance;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Isolation\Database\InstanceDatabaseContext;
use CmsOrbit\Saas\Support\HostConnection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class PostSyncService
{
    /**
     * @return array{total: int, published: int}
     */
    public function counts(Instance $instance): array
    {
        if (! $this->instanceDatabaseExists($instance)) {
            return [
                'total' => 0,
                'published' => 0,
            ];
        }

        try {
            return $this->runOnInstance($instance, function (): array {
                return [
                    'total' => Post::query()->count(),
                    'published' => Post::query()->published()->count(),
                ];
            });
        } catch (\Throwable) {
            return [
                'total' => 0,
                'published' => 0,
            ];
        }
    }

    public function resolveSourceInstance(): ?Instance
    {
        $container = \CmsOrbit\Saas\Models\Container::query()->where('slug', 'blog')->first();

        if ($container === null) {
            return null;
        }

        $preferred = Instance::query()
            ->whereBelongsTo($container, 'container')
            ->whereHas('routeEndpoints', fn ($query) => $query->where('value', 'blog'))
            ->first();

        if ($preferred instanceof Instance) {
            return $preferred;
        }

        return Instance::query()
            ->whereBelongsTo($container, 'container')
            ->oldest('created_at')
            ->first();
    }

    /**
     * @return array{synced: int, source_id: string|null}
     */
    public function syncToInstance(Instance $target, ?Instance $source = null): array
    {
        $source ??= $this->resolveSourceInstance();

        if (! $source instanceof Instance) {
            throw new \RuntimeException(__('No source blog instance is available for post sync.'));
        }

        if (! $this->instanceDatabaseExists($source)) {
            throw new \RuntimeException(__('The catalog blog instance database is not provisioned yet.'));
        }

        if ($source->getKey() === $target->getKey()) {
            throw new \RuntimeException(__('Choose a different instance to sync into.'));
        }

        /** @var list<array<string, mixed>> $payload */
        $payload = $this->runOnInstance($source, function (): array {
            return Post::query()
                ->get(['title', 'slug', 'body', 'excerpt', 'status', 'published_at', 'featured_image', 'meta_title', 'meta_description'])
                ->map(fn (Post $post): array => [
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'body' => $post->body,
                    'excerpt' => $post->excerpt,
                    'status' => $post->status?->value,
                    'published_at' => $post->published_at instanceof Carbon
                        ? $post->published_at->toDateTimeString()
                        : $post->published_at,
                    'featured_image' => $post->featured_image,
                    'meta_title' => $post->meta_title,
                    'meta_description' => $post->meta_description,
                ])
                ->all();
        });

        if (! $this->instanceDatabaseExists($target)) {
            throw new \RuntimeException(__('The target blog instance database is not provisioned yet.'));
        }

        $synced = $this->runOnInstance($target, function () use ($payload): int {
            $count = 0;

            foreach ($payload as $attributes) {
                Post::query()->updateOrCreate(
                    ['slug' => $attributes['slug']],
                    $attributes,
                );
                $count++;
            }

            return $count;
        });

        saas()->host(function () use ($target, $source, $synced): void {
            $target->setInternal('post_sync', [
                'last_synced_at' => now()->toIso8601String(),
                'source_instance_id' => (string) $source->getKey(),
                'synced_count' => $synced,
            ])->save();
        });

        return [
            'synced' => $synced,
            'source_id' => (string) $source->getKey(),
        ];
    }

    /**
     * @return array{last_synced_at: string|null, source_instance_id: string|null, synced_count: int|null}
     */
    /**
     * @return list<array<string, mixed>>
     */
    public function listPosts(Instance $instance): array
    {
        if (! $this->instanceDatabaseExists($instance)) {
            return [];
        }

        try {
            return $this->runOnInstance($instance, function () use ($instance): array {
                return Post::query()
                    ->orderByDesc('published_at')
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(fn (Post $post): array => $this->serializePost($instance, $post))
                    ->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAllPosts(?\CmsOrbit\Saas\Models\Container $container = null): array
    {
        $container ??= \CmsOrbit\Saas\Models\Container::query()->where('slug', 'blog')->first();

        if ($container === null) {
            return [];
        }

        $posts = [];

        $instances = Instance::query()
            ->whereBelongsTo($container, 'container')
            ->with('routeEndpoints')
            ->get();

        foreach ($instances as $instance) {
            foreach ($this->listPosts($instance) as $post) {
                $endpoint = $instance->primaryEndpoint() ?? $instance->fallbackEndpoint();

                $posts[] = array_merge($post, [
                    'instanceId' => (string) $instance->getKey(),
                    'instanceName' => $instance->name,
                    'instancePath' => $endpoint?->normalizedValue() ?? '—',
                    'postsUrl' => Route::has('orbit.blog.posting.instance')
                        ? route('orbit.blog.posting.instance', ['instanceId' => (string) $instance->getKey()])
                        : null,
                ]);
            }
        }

        usort($posts, function (array $a, array $b): int {
            $publishedCompare = strcmp((string) ($b['publishedAt'] ?? ''), (string) ($a['publishedAt'] ?? ''));

            if ($publishedCompare !== 0) {
                return $publishedCompare;
            }

            return strcmp((string) ($a['instanceName'] ?? ''), (string) ($b['instanceName'] ?? ''));
        });

        return $posts;
    }

    public function findPost(Instance $instance, int $postId): ?Post
    {
        if (! $this->instanceDatabaseExists($instance)) {
            return null;
        }

        try {
            return $this->runOnInstance($instance, fn (): ?Post => Post::query()->find($postId));
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updatePost(Instance $instance, int $postId, array $attributes): Post
    {
        if (! $this->instanceDatabaseExists($instance)) {
            throw new \RuntimeException(__('The target blog instance database is not provisioned yet.'));
        }

        return $this->runOnInstance($instance, function () use ($postId, $attributes): Post {
            /** @var Post $post */
            $post = Post::query()->findOrFail($postId);

            $post->fill($attributes);

            if (blank($post->slug) && filled($post->title)) {
                $post->slug = Post::uniqueSlug((string) $post->title, $post->getKey());
            }

            $post->save();

            return $post->fresh() ?? $post;
        });
    }

    public function publicPostUrl(Instance $instance, string $slug): ?string
    {
        $endpoint = $instance->primaryEndpoint() ?? $instance->fallbackEndpoint();

        if (! $endpoint instanceof RouteEndpoint) {
            return null;
        }

        return $endpoint->canonicalUrl($slug);
    }

    /**
     * @return array<string, mixed>
     */
    public function serializePost(Instance $instance, Post $post): array
    {
        $instanceId = (string) $instance->getKey();
        $postId = (int) $post->getKey();

        return [
            'id' => $postId,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'status' => $post->status?->value,
            'statusLabel' => $post->status?->label() ?? '—',
            'publishedAt' => $post->published_at?->toDateTimeString(),
            'featuredImage' => $post->featured_image,
            'metaTitle' => $post->meta_title,
            'metaDescription' => $post->meta_description,
            'publicUrl' => $this->publicPostUrl($instance, (string) $post->slug),
            'viewUrl' => Route::has('orbit.blog.posting.posts.view')
                ? route('orbit.blog.posting.posts.view', ['instanceId' => $instanceId, 'postId' => $postId])
                : null,
            'editUrl' => Route::has('orbit.blog.posting.posts.edit')
                ? route('orbit.blog.posting.posts.edit', ['instanceId' => $instanceId, 'postId' => $postId])
                : null,
        ];
    }

    public function syncMeta(Instance $instance): array
    {
        $meta = $instance->getInternal('post_sync');

        if (! is_array($meta)) {
            return [
                'last_synced_at' => null,
                'source_instance_id' => null,
                'synced_count' => null,
            ];
        }

        return [
            'last_synced_at' => isset($meta['last_synced_at']) ? (string) $meta['last_synced_at'] : null,
            'source_instance_id' => isset($meta['source_instance_id']) ? (string) $meta['source_instance_id'] : null,
            'synced_count' => isset($meta['synced_count']) ? (int) $meta['synced_count'] : null,
        ];
    }

    public function instanceDatabaseExists(Instance $instance): bool
    {
        if (HostConnection::isSqlite()) {
            return file_exists($instance->getDatabasePath());
        }

        $driver = HostConnection::driver();

        $managerClass = config("saas.database.managers.{$driver}");

        if (! is_string($managerClass) || ! class_exists($managerClass)) {
            return false;
        }

        $context = new InstanceDatabaseContext(
            databaseName: $instance->getDatabaseName(),
            connectionName: HostConnection::name(),
        );

        return app($managerClass)->databaseExists($context);
    }

    /**
     * @template T
     * @param  \Closure(): T  $callback
     * @return T
     */
    protected function runOnInstance(Instance $instance, \Closure $callback): mixed
    {
        try {
            return saas()->run($instance, $callback);
        } finally {
            saas()->end();
            DB::purge('instance');
            DB::setDefaultConnection(BlogDatabaseConnection::name());
        }
    }
}
