<?php

declare(strict_types=1);

namespace CmsOrbit\Blog;

use CmsOrbit\Blog\Admin\SignedAdminUrlGenerator;
use CmsOrbit\Blog\Entities\CategoryEntity;
use CmsOrbit\Blog\Entities\PostEntity;
use CmsOrbit\Blog\Entities\TagEntity;
use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Models\Post;
use CmsOrbit\Blog\Models\Tag;
use CmsOrbit\Blog\Policies\CategoryPolicy;
use CmsOrbit\Blog\Policies\PostPolicy;
use CmsOrbit\Blog\Policies\TagPolicy;
use CmsOrbit\Blog\Provisioning\BlogDefaultInstanceProvisioner;
use CmsOrbit\Blog\Routing\BlogPublicRouteRegistrar;
use CmsOrbit\Blog\View\ThemeViewComposer;
use CmsOrbit\Core\Foundation\Entity\EntityRegistry;
use CmsOrbit\Core\Foundation\ItemPermission;
use CmsOrbit\Core\Foundation\OrbitServiceProvider;
use CmsOrbit\Core\Screen\Actions\Menu;
use CmsOrbit\Core\Support\Facades\Orbit;
use CmsOrbit\Core\Support\Locale;
use CmsOrbit\Saas\Admin\HostContainerAdminRegistrar;
use CmsOrbit\Saas\Theme\Theme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class BlogServiceProvider extends OrbitServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blog.php', 'blog');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\SeedDemosCommand::class,
            ]);
        }

        require_once __DIR__.'/Support/helpers.php';

        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        Locale::registerPath(__DIR__.'/../resources/lang');

        $entities = [PostEntity::class, CategoryEntity::class, TagEntity::class];

        $this->app->afterResolving(EntityRegistry::class, function (EntityRegistry $registry) use ($entities): void {
            $registry->registerClass($entities);
        });

        if ($this->app->resolved(EntityRegistry::class)) {
            $this->app->make(EntityRegistry::class)->registerClass($entities);
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog-package');
        $this->loadViewsFrom(__DIR__.'/../container/themes/_base/views', 'blog-theme-base');

        $this->registerThemes();
        $this->registerPolicies();
        $this->registerBlogRoute();
        $this->registerBlogPublicRoutes();
        $this->registerPermissions();
        $this->registerViewComposers();

        $this->app->booted(function (): void {
            $this->registerBlogMenu();
            $this->provisionDefaultInstance();
        });
    }

    public function registerAdminNavigation(): void
    {
        $this->registerBlogMenu();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
    }

    protected function registerThemes(): void
    {
        $themesPath = dirname(__DIR__).'/container/themes';

        foreach (glob($themesPath.'/*/ThemeServiceProvider.php') ?: [] as $providerFile) {
            $slug = basename(dirname($providerFile));

            if ($slug === '_base') {
                continue;
            }

            require_once $providerFile;

            $class = 'CmsOrbit\\Blog\\Themes\\'.Str::studly($slug).'\\ThemeServiceProvider';

            if (! class_exists($class)) {
                continue;
            }

            Theme::register('blog', $slug, $class);
        }
    }

    protected function registerPermissions(): void
    {
        $group = ItemPermission::group(__('Blog'))
            ->addPermission('blog.dashboard', __('Blog Hub'));

        foreach (['blog-posts', 'blog-categories', 'blog-tags'] as $uriKey) {
            $group
                ->addPermission("blog.entities.{$uriKey}.viewAny", __("View {$uriKey}"))
                ->addPermission("blog.entities.{$uriKey}.view", __("View {$uriKey} item"))
                ->addPermission("blog.entities.{$uriKey}.create", __("Create {$uriKey}"))
                ->addPermission("blog.entities.{$uriKey}.update", __("Update {$uriKey}"))
                ->addPermission("blog.entities.{$uriKey}.delete", __("Delete {$uriKey}"));
        }

        Orbit::registerPermission($group);
    }

    protected function registerViewComposers(): void
    {
        View::composer(['blog-package::*', 'blog-theme-*::*'], ThemeViewComposer::class);
    }

    protected function provisionDefaultInstance(): void
    {
        if ($this->app->runningUnitTests()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            app(BlogDefaultInstanceProvisioner::class)->provisionIfNeeded();
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    protected function registerBlogRoute(): void
    {
        Route::domain($this->resolveOrbitDomain())
            ->prefix($this->resolveOrbitPrefix())
            ->as('orbit.')
            ->middleware(config('orbit.middleware.private'))
            ->group(__DIR__.'/../routes/orbit.php');
    }

    protected function registerBlogPublicRoutes(): void
    {
        app(BlogPublicRouteRegistrar::class)->register();
    }

    protected function registerBlogMenu(): void
    {
        $sectionKey = HostContainerAdminRegistrar::sectionKey('blog');
        $hubUrl = Route::has('orbit.blog.index') ? route('orbit.blog.index') : '#';
        $instancesUrl = Route::has('orbit.blog.instances.index') ? route('orbit.blog.instances.index') : '#';
        $postingUrl = Route::has('orbit.blog.posting.index') ? route('orbit.blog.posting.index') : '#';

        Orbit::registerMenuElement(
            Menu::make(__('Blog Management'))
                ->icon('bs.pencil-square')
                ->url($hubUrl)
                ->sort(5500)
                ->set('section', __('Blog'))
                ->set('sectionKey', $sectionKey)
                ->set('permission', 'blog.dashboard')
                ->active([
                    $hubUrl,
                    $hubUrl.'?*',
                    $instancesUrl,
                    $instancesUrl.'?*',
                    $instancesUrl.'/*',
                    $postingUrl,
                    $postingUrl.'?*',
                    $postingUrl.'/*',
                ])
                ->list([
                    Menu::make(__('Overview'))
                        ->icon('bs.grid')
                        ->url($hubUrl)
                        ->active([
                            $hubUrl,
                            $hubUrl.'?*',
                        ]),

                    Menu::make(__('Blog Instances'))
                        ->icon('bs.collection')
                        ->url($instancesUrl)
                        ->active([
                            $instancesUrl,
                            $instancesUrl.'?*',
                            $instancesUrl.'/*',
                        ]),

                    Menu::make(__('Posting'))
                        ->icon('bs.journal-text')
                        ->url($postingUrl)
                        ->active([
                            $postingUrl,
                            $postingUrl.'?*',
                            $postingUrl.'/*',
                        ]),
                ])
        );
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
