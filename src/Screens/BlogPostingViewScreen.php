<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Services\PostSyncService;
use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Screen\Sight;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;

class BlogPostingViewScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('View post');
    }

    public function description(): ?string
    {
        return __('Read-only preview of a synced blog post.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(PostSyncService $sync, string $instanceId, int $postId): array
    {
        $instance = $this->findBlogInstance($instanceId);
        $post = $sync->findPost($instance, $postId);

        abort_if($post === null, 404);

        $serialized = $sync->serializePost($instance, $post);

        return [
            'instance' => [
                'id' => $instance->getKey(),
                'name' => $instance->name,
            ],
            'post' => [
                'id' => $serialized['id'],
                'title' => $serialized['title'],
                'slug' => $serialized['slug'],
                'statusLabel' => $serialized['statusLabel'],
                'publishedAt' => $serialized['publishedAt'] ?? '—',
                'excerpt' => $serialized['excerpt'] ?: '—',
                'body' => $serialized['body'] ?: '—',
                'featuredImage' => $serialized['featuredImage'] ?: '—',
                'metaTitle' => $serialized['metaTitle'] ?: '—',
                'metaDescription' => $serialized['metaDescription'] ?: '—',
                'publicUrl' => $serialized['publicUrl'],
                'editUrl' => $serialized['editUrl'],
            ],
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $instanceId = (string) request()->route('instanceId');
        $postId = (int) request()->route('postId');
        $instance = $this->findBlogInstance($instanceId);
        $post = app(PostSyncService::class)->findPost($instance, $postId);

        abort_if($post === null, 404);

        $serialized = app(PostSyncService::class)->serializePost($instance, $post);

        $actions = [
            Link::make(__('Back to posts'))
                ->icon('bs.arrow-left')
                ->route('orbit.blog.posting.instance', ['instanceId' => $instanceId]),
        ];

        if (filled($serialized['editUrl'] ?? null)) {
            $actions[] = Link::make(__('Edit post'))
                ->icon('bs.pencil')
                ->href((string) $serialized['editUrl']);
        }

        if (filled($serialized['publicUrl'] ?? null)) {
            $actions[] = Link::make(__('Open on blog'))
                ->icon('bs.box-arrow-up-right')
                ->target('_blank')
                ->href((string) $serialized['publicUrl']);
        }

        return $actions;
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::legend('post', [
                Sight::make('title', __('Title')),
                Sight::make('slug', __('Slug')),
                Sight::make('statusLabel', __('Status')),
                Sight::make('publishedAt', __('Published')),
                Sight::make('excerpt', __('Excerpt')),
                Sight::make('body', __('Body')),
                Sight::make('featuredImage', __('Featured Image URL')),
                Sight::make('metaTitle', __('Meta Title')),
                Sight::make('metaDescription', __('Meta Description')),
            ])->title(__('Post details')),
        ];
    }
}
