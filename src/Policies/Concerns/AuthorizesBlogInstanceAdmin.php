<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Policies\Concerns;

use CmsOrbit\Core\Foundation\Models\User;

trait AuthorizesBlogInstanceAdmin
{
    protected function hasBlogAdminSession(?User $user): bool
    {
        if ($user === null || instance_context() === null) {
            return false;
        }

        $instanceId = (string) request()->session()->get('blog_admin_instance_id');
        $userId = (string) request()->session()->get('blog_admin_user_id');
        $expires = (int) request()->session()->get('blog_admin_expires_at');

        return $instanceId === (string) instance_context()->instance->getKey()
            && $userId === (string) $user->getKey()
            && $expires >= now()->timestamp;
    }
}
