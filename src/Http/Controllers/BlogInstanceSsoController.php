<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Admin\SignedAdminUrlGenerator;
use CmsOrbit\Blog\Support\BlogDatabaseConnection;
use CmsOrbit\Core\Foundation\Models\User;
use CmsOrbit\Saas\Enums\EndpointType;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogInstanceSsoController
{
    public function __construct(protected SignedAdminUrlGenerator $urls) {}

    public function __invoke(Request $request): RedirectResponse
    {
        if (! $this->urls->validate($request->query())) {
            abort(403, __('Invalid or expired admin link.'));
        }

        $userId = (string) $request->query('user');
        $expires = (int) $request->query('expires');
        $instanceId = (string) $request->query('instance');

        $request->session()->put([
            'blog_admin_instance_id' => $instanceId,
            'blog_admin_user_id' => $userId,
            'blog_admin_expires_at' => $expires,
        ]);

        $hostConnection = BlogDatabaseConnection::name();

        saas()->host(function () use ($userId, $hostConnection): void {
            $user = User::on($hostConnection)->find($userId);

            if ($user !== null) {
                Auth::guard(config('orbit.guard', 'web'))->setUser($user);
            }
        });

        return redirect($this->adminDashboardPath());
    }

    protected function adminDashboardPath(): string
    {
        $prefix = trim(blog_admin_prefix(), '/');
        $context = instance_context();
        $endpoint = $context?->instance->primaryEndpoint();

        if ($endpoint instanceof RouteEndpoint && $endpoint->type === EndpointType::Path) {
            return '/'.trim($endpoint->value, '/').'/'.$prefix;
        }

        return '/'.$prefix;
    }
}
