<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Entities;

use CmsOrbit\Blog\Concerns\HasBlogPermissions;
use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\TextArea;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CategoryEntity extends Entity
{
    use HasBlogPermissions;

    public static function uriKey(): string
    {
        return 'blog-categories';
    }

    public function model(): string
    {
        return Category::class;
    }

    public function icon(): string
    {
        return 'bs.folder';
    }

    public function sort(): int
    {
        return 5210;
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
        return __('Categories');
    }

    public function singularLabel(): string
    {
        return __('Category');
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
            TextArea::make('description')->title(__('Description'))->rows(3),
            Input::make('sort')->title(__('Sort'))->type('number')->value(0),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('id', __('ID'))->sort(),
            TD::make('name', __('Name')),
            TD::make('slug', __('Slug')),
            TD::make('sort', __('Sort'))->sort(),
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
                Rule::unique('categories', 'slug')->ignore($model->getKey()),
            ],
        ];
    }
}
