<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Middleware;

use Closure;
use CmsOrbit\Core\Foundation\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlogInstanceAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = instance_context();

        if ($context === null) {
            abort(404);
        }

        $instanceId = (string) $request->session()->get('blog_admin_instance_id');
        $userId = (string) $request->session()->get('blog_admin_user_id');
        $expires = (int) $request->session()->get('blog_admin_expires_at');

        if ($instanceId !== (string) $context->instance->getKey()
            || $userId === ''
            || $expires < now()->timestamp) {
            abort(403, __('Blog admin session expired.'));
        }

        saas()->host(function () use ($userId): void {
            $user = User::query()->find($userId);

            if ($user !== null) {
                Auth::guard(config('orbit.guard', 'web'))->setUser($user);
            }
        });

        if (Auth::guard(config('orbit.guard', 'web'))->guest()) {
            abort(403, __('Blog admin session expired.'));
        }

        return $next($request);
    }
}
