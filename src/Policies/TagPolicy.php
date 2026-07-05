<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Policies;

use CmsOrbit\Blog\Models\Tag;
use CmsOrbit\Blog\Policies\Concerns\AuthorizesBlogInstanceAdmin;
use CmsOrbit\Core\Foundation\Models\User;

class TagPolicy
{
    use AuthorizesBlogInstanceAdmin;

    public function viewAny(?User $user): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function view(?User $user, Tag $tag): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function create(?User $user): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function update(?User $user, Tag $tag): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function delete(?User $user, Tag $tag): bool
    {
        return $this->hasBlogAdminSession($user);
    }
}
