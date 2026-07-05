<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Admin;

use CmsOrbit\Blog\Entities\CategoryEntity;
use CmsOrbit\Blog\Entities\PostEntity;
use CmsOrbit\Blog\Entities\TagEntity;
use CmsOrbit\Blog\Http\Middleware\BlogInstanceAccess;
use CmsOrbit\Blog\Http\Middleware\BlogInstanceAdminAuth;
use CmsOrbit\Blog\Screens\BlogInstanceAdminScreen;
use CmsOrbit\Blog\Screens\ThemeSettingsScreen;
use CmsOrbit\Core\Config\Screens\ConfigGroupScreen;
use CmsOrbit\Core\Crud\Screens\CreateScreen;
use CmsOrbit\Core\Crud\Screens\EditScreen;
use CmsOrbit\Core\Crud\Screens\ListScreen;
use CmsOrbit\Core\Crud\Screens\TrashScreen;
use CmsOrbit\Core\Crud\Screens\ViewScreen;
use CmsOrbit\Core\Foundation\Entity\Entity;
use CmsOrbit\Core\Foundation\Entity\EntityRegistry;
use CmsOrbit\Core\Foundation\Http\Middleware\SetOrbitLocale;
use CmsOrbit\Core\Filters\Http\Middleware\NormalizeTableFilterQuery;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

class BlogInstanceAdminRouteRegistrar
{
    public function register(string $prefix): void
    {
        $middleware = [
            'web',
            BlogInstanceAdminAuth::class,
            SetOrbitLocale::class,
            NormalizeTableFilterQuery::class,
        ];

        Route::prefix($prefix)
            ->middleware($middleware)
            ->as('blog.admin.')
            ->group(function (): void {
                Route::screen('/', BlogInstanceAdminScreen::class)
                    ->middleware(BlogInstanceAccess::class.':blog.dashboard')
                    ->name('index');

                Route::screen('theme-settings', ThemeSettingsScreen::class)
                    ->middleware(BlogInstanceAccess::class.':blog.dashboard')
                    ->name('theme-settings');

                $this->registerEntities();
            });
    }

    protected function registerEntities(): void
    {
        $entities = [
            PostEntity::class,
            CategoryEntity::class,
            TagEntity::class,
        ];

        foreach ($entities as $entityClass) {
            /** @var Entity $entity */
            $entity = app($entityClass);
            $this->registerEntityRoutes($entity);
        }
    }

    protected function registerEntityRoutes(Entity $entity): void
    {
        $key = $entity::uriKey();
        $name = 'entities.'.$key;
        $full = 'blog.admin.'.$name;
        $base = 'entities/'.$key;

        if ($entity->hasCrud('create')) {
            Route::screen($base.'/create', $entity->screenFor('create', CreateScreen::class))
                ->middleware(BlogInstanceAccess::class.':'.$entity->abilityPermission('create'))
                ->name($name.'.create')
                ->defaults('entity', $key)
                ->breadcrumbs(fn (Trail $trail) => $trail
                    ->parent($full.'.index')
                    ->push(__('Create')));
        }

        if ($entity->hasCrud('edit')) {
            Route::screen($base.'/{id}/edit', $entity->screenFor('edit', EditScreen::class))
                ->middleware(BlogInstanceAccess::class.':'.$entity->abilityPermission('update'))
                ->name($name.'.edit')
                ->defaults('entity', $key)
                ->breadcrumbs(fn (Trail $trail) => $trail
                    ->parent($entity->hasCrud('view') ? $full.'.view' : $full.'.index')
                    ->push(__('Edit')));
        }

        if ($entity->hasCrud('trash')) {
            Route::screen($base.'/trash', $entity->screenFor('trash', TrashScreen::class))
                ->middleware(BlogInstanceAccess::class.':'.$entity->abilityPermission('delete'))
                ->name($name.'.trash')
                ->defaults('entity', $key)
                ->breadcrumbs(fn (Trail $trail) => $trail
                    ->parent($full.'.index')
                    ->push(__('Trash')));
        }

        if ($entity->hasCrud('view')) {
            Route::screen($base.'/{id}', $entity->screenFor('view', ViewScreen::class))
                ->middleware(BlogInstanceAccess::class.':'.$entity->abilityPermission('view'))
                ->name($name.'.view')
                ->defaults('entity', $key)
                ->breadcrumbs(fn (Trail $trail) => $trail
                    ->parent($full.'.index')
                    ->push((string) request()->route('id')));
        }

        if ($entity->hasCrud('list')) {
            Route::screen($base, $entity->screenFor('list', ListScreen::class))
                ->middleware(BlogInstanceAccess::class.':'.$entity->abilityPermission('viewAny'))
                ->name($name.'.index')
                ->defaults('entity', $key)
                ->breadcrumbs(fn (Trail $trail) => $trail
                    ->parent('blog.admin.index')
                    ->push($entity->label()));
        }
    }
}
