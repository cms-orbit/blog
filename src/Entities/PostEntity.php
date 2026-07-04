<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Entities;

use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\TextArea;
use CmsOrbit\Core\Screen\TD;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;

class PostEntity extends Entity
{
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
}
