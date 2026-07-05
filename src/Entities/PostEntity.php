<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Entities;

use CmsOrbit\Blog\Concerns\HasBlogPermissions;
use CmsOrbit\Blog\Enums\PostStatus;
use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\Select;
use CmsOrbit\Core\Screen\Fields\TextArea;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PostEntity extends Entity
{
    use HasBlogPermissions;

    public static function uriKey(): string
    {
        return 'blog-posts';
    }

    public function model(): string
    {
        return Post::class;
    }

    public function icon(): string
    {
        return 'bs.journal-text';
    }

    public function sort(): int
    {
        return 5200;
    }

    public function section(): string
    {
        return __('Blog');
    }

    public function sectionKey(): string
    {
        return HostContainerAdminRegistrar::sectionKey('blog');
    }

    public function label(): string
    {
        return __('Blog Posts');
    }

    public function singularLabel(): string
    {
        return __('Blog Post');
    }

    public function displayInNavigation(): bool
    {
        return instance_context() !== null;
    }

    public function newModel(): Model
    {
        $this->ensureInstanceContext();

        return parent::newModel();
    }

    public function query(): Builder
    {
        $this->ensureInstanceContext();

        return parent::query()->with(['category', 'tags']);
    }

    public function fields(): array
    {
        return [
            Input::make('title')->title(__('Title'))->required(),
            Input::make('slug')->title(__('Slug'))->help(__('Leave blank to auto-generate from title.')),
            TextArea::make('excerpt')->title(__('Excerpt'))->rows(3),
            TextArea::make('body')->title(__('Body'))->rows(8),
            Select::make('status')->title(__('Status'))
                ->options(PostStatus::options())
                ->value(PostStatus::Draft->value),
            Input::make('published_at')->title(__('Published At'))->type('datetime-local'),
            Select::make('category_id')->title(__('Category'))
                ->fromModel(Category::class, 'name', 'id')
                ->empty(__('No category')),
            Select::make('tags.')
                ->title(__('Tags'))
                ->fromModel(\CmsOrbit\Blog\Models\Tag::class, 'name', 'id')
                ->multiple(),
            Input::make('featured_image')->title(__('Featured Image URL')),
            Input::make('meta_title')->title(__('Meta Title')),
            Input::make('meta_description')->title(__('Meta Description')),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id', __('ID'))->sort(),
            TD::make('title', __('Title')),
            TD::make('status', __('Status'))
                ->render(fn (Post $post) => $post->status?->label() ?? '—')
                ->filter(TD::FILTER_SELECT, PostStatus::options()),
            TD::make('category.name', __('Category'))
                ->filter(TD::FILTER_SELECT, Category::query()->pluck('name', 'id')->all()),
            TD::make('published_at', __('Published'))->sort(),
            TD::make('created_at', __('Created'))->sort(),
        ];
    }

    public function rules(Model $model): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($model->getKey()),
            ],
            'status' => ['required', Rule::enum(PostStatus::class)],
        ];
    }

    protected function ensureInstanceContext(): void
    {
        abort_if(instance_context() === null, 404);
    }
}
