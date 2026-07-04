<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Entities;

use CmsOrbit\Blog\Concerns\HasBlogPermissions;
use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\TextArea;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        return false;
    }

    public function newModel(): Model
    {
        $this->ensureInstanceContext();

        return parent::newModel();
    }

    public function query(): Builder
    {
        $this->ensureInstanceContext();

        return parent::query();
    }

    public function fields(): array
    {
        return [
            Input::make('title')->title(__('Title'))->required(),
            TextArea::make('body')->title(__('Body'))->rows(6),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id', __('ID'))->sort(),
            TD::make('title', __('Title')),
            TD::make('created_at', __('Created'))->sort(),
        ];
    }

    protected function ensureInstanceContext(): void
    {
        abort_if(instance_context() === null, 404);
    }
}
