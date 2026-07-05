<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Entities;

use CmsOrbit\Blog\Concerns\HasBlogPermissions;
use CmsOrbit\Blog\Models\Tag;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TagEntity extends Entity
{
    use HasBlogPermissions;

    public static function uriKey(): string
    {
        return 'blog-tags';
    }

    public function model(): string
    {
        return Tag::class;
    }

    public function icon(): string
    {
        return 'bs.tags';
    }

    public function sort(): int
    {
        return 5220;
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
        return __('Tags');
    }

    public function singularLabel(): string
    {
        return __('Tag');
    }

    public function displayInNavigation(): bool
    {
        return instance_context() !== null;
    }

    public function query(): Builder
    {
        abort_if(instance_context() === null, 404);

        return parent::query();
    }

    public function fields(): array
    {
        return [
            Input::make('name')->title(__('Name'))->required(),
            Input::make('slug')->title(__('Slug'))->help(__('Leave blank to auto-generate from name.')),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id', __('ID'))->sort(),
            TD::make('name', __('Name')),
            TD::make('slug', __('Slug')),
        ];
    }

    public function rules(Model $model): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')->ignore($model->getKey()),
            ],
        ];
    }
}
