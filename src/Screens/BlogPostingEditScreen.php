<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Enums\PostStatus;
use CmsOrbit\Blog\Services\PostSyncService;
use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Button;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\Select;
use CmsOrbit\Core\Screen\Fields\RichText;
use CmsOrbit\Core\Screen\Fields\TextArea;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Core\Support\Facades\Toast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogPostingEditScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Edit post');
    }

    public function description(): ?string
    {
        return __('Update a synced blog post without leaving the host workspace.');
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

        return [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'status' => $post->status?->value ?? PostStatus::Draft->value,
                'published_at' => $post->published_at?->format('Y-m-d\TH:i'),
                'featured_image' => $post->featured_image,
                'meta_title' => $post->meta_title,
                'meta_description' => $post->meta_description,
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

        return [
            Link::make(__('Back to post'))
                ->icon('bs.arrow-left')
                ->route('orbit.blog.posting.posts.view', [
                    'instanceId' => $instanceId,
                    'postId' => $postId,
                ]),

            Button::make(__('Save changes'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::rows([
                Input::make('post.title')->title(__('Title'))->required(),
                Input::make('post.slug')->title(__('Slug')),
                TextArea::make('post.excerpt')->title(__('Excerpt'))->rows(3),
                RichText::make('post.body')->title(__('Body')),
                Select::make('post.status')->title(__('Status'))
                    ->options(PostStatus::options())
                    ->required(),
                Input::make('post.published_at')->title(__('Published At'))->type('datetime-local'),
                Input::make('post.featured_image')->title(__('Featured Image URL')),
                Input::make('post.meta_title')->title(__('Meta Title')),
                Input::make('post.meta_description')->title(__('Meta Description')),
            ])->title(__('Post content')),
        ];
    }

    public function save(Request $request, PostSyncService $sync): RedirectResponse
    {
        $instanceId = (string) $request->route('instanceId');
        $postId = (int) $request->route('postId');
        $instance = $this->findBlogInstance($instanceId);

        $validated = $request->validate([
            'post.title' => ['required', 'string', 'max:255'],
            'post.slug' => ['nullable', 'string', 'max:255'],
            'post.excerpt' => ['nullable', 'string'],
            'post.body' => ['nullable', 'string'],
            'post.status' => ['required', Rule::enum(PostStatus::class)],
            'post.published_at' => ['nullable', 'date'],
            'post.featured_image' => ['nullable', 'string', 'max:2048'],
            'post.meta_title' => ['nullable', 'string', 'max:255'],
            'post.meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var array<string, mixed> $attributes */
        $attributes = $validated['post'];

        try {
            $sync->updatePost($instance, $postId, $attributes);
        } catch (\Throwable $exception) {
            Toast::error($exception->getMessage());

            return redirect()->back();
        }

        Toast::success(__('The post was updated.'));

        return redirect()->route('orbit.blog.posting.posts.view', [
            'instanceId' => $instanceId,
            'postId' => $postId,
        ]);
    }
}
