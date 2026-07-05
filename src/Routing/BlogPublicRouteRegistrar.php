<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Routing;

use CmsOrbit\Blog\Http\Controllers\PublicHubController;
use CmsOrbit\Blog\Support\BlogContainerDomain;
use Illuminate\Support\Facades\Route;

class BlogPublicRouteRegistrar
{
    public function register(): void
    {
        Route::domain(BlogContainerDomain::host())
            ->middleware('web')
            ->group(function (): void {
                Route::get('/', PublicHubController::class)->name('blog.public.hub');
            });
    }
}
