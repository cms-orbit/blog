<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Middleware;

use Closure;
use CmsOrbit\Core\Foundation\Http\Middleware\SetOrbitLocale;
use CmsOrbit\Core\Filters\Http\Middleware\NormalizeTableFilterQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogInstanceAccess
{
    public function handle(Request $request, Closure $next, string $permission = 'blog.dashboard'): Response
    {
        $user = auth()->guard(config('orbit.guard', 'web'))->user();

        if ($user === null) {
            abort(403);
        }

        if (method_exists($user, 'hasAccess') && ! $user->hasAccess($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
