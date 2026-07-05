<?php

declare(strict_types=1);

namespace CmsOrbit\Blog;

use CmsOrbit\Blog\Entities\PostEntity;
use CmsOrbit\Core\Foundation\Entity\EntityRegistry;
use CmsOrbit\Core\Foundation\ItemPermission;
use CmsOrbit\Core\Foundation\OrbitServiceProvider;
use CmsOrbit\Core\Screen\Actions\Menu;
use CmsOrbit\Core\Support\Facades\Orbit;
use CmsOrbit\Core\Support\Locale;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use CmsOrbit\Saas\Theme\Theme;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BlogServiceProvider extends OrbitServiceProvider
{
    public function register(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        Locale::registerPath(__DIR__.'/../resources/lang');

        $this->app->afterResolving(EntityRegistry::class, function (EntityRegistry $registry): void {
            $registry->registerClass([PostEntity::class]);
        });

        if ($this->app->resolved(EntityRegistry::class)) {
            $this->app->make(EntityRegistry::class)->registerClass([PostEntity::class]);
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog');

        Theme::register('blog', 'default', BlogThemeServiceProvider::class);

        $this->registerBlogRoute();

        Orbit::registerPermission(
            ItemPermission::group(__('Blog'))
                ->addPermission('blog.dashboard', __('Blog Hub'))
        );

        $this->app->booted(function (): void {
            $this->registerBlogMenu();
        });
    }

    protected function registerBlogRoute(): void
    {
        Route::domain($this->resolveOrbitDomain())
            ->prefix($this->resolveOrbitPrefix())
            ->as('orbit.')
            ->middleware(config('orbit.middleware.private'))
            ->group(__DIR__.'/../routes/orbit.php');
    }

    protected function registerBlogMenu(): void
    {
        $sectionKey = HostContainerAdminRegistrar::sectionKey('blog');
        $hubUrl = Route::has('orbit.blog.index') ? route('orbit.blog.index') : '#';

        Orbit::registerMenuElement(
            Menu::make(__('Blog Management'))
                ->icon('bs.pencil-square')
                ->url($hubUrl)
                ->sort(5500)
                ->set('section', __('Blog'))
                ->set('sectionKey', $sectionKey)
                ->set('permission', 'blog.dashboard')
                ->list([
                    Menu::make(__('Overview'))
                        ->icon('bs.grid')
                        ->url($hubUrl),

                    Menu::make(__('Blog Instances'))
                        ->icon('bs.collection')
                        ->url($this->hubAnchorUrl($hubUrl, 'instances')),

                    Menu::make(__('Routing Overview'))
                        ->icon('bs.signpost-split')
                        ->url($this->hubAnchorUrl($hubUrl, 'routing')),

                    Menu::make(__('Container Details'))
                        ->icon('bs.box-seam')
                        ->url($this->hubAnchorUrl($hubUrl, 'container')),
                ])
        );
    }

    protected function hubAnchorUrl(string $hubUrl, string $anchor): string
    {
        return $hubUrl === '#' ? '#' : $hubUrl.'#'.$anchor;
    }

    protected function resolveOrbitDomain(): ?string
    {
        return match (config('orbit.access.mode', 'subdomain')) {
            'subdomain' => $this->resolveOrbitSubdomainHost(),
            'domain' => config('orbit.access.domain'),
            default => null,
        };
    }

    protected function resolveOrbitSubdomainHost(): ?string
    {
        $label = (string) config('orbit.access.subdomain', 'orbit');
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return $label.'.'.$host;
    }

    protected function resolveOrbitPrefix(): string
    {
        if (config('orbit.access.mode') === 'path') {
            return Str::start((string) config('orbit.access.prefix', 'settings'), '/');
        }

        return '/';
    }
}
