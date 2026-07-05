<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Policies;

use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Policies\Concerns\AuthorizesBlogInstanceAdmin;
use CmsOrbit\Core\Foundation\Models\User;

class CategoryPolicy
{
    use AuthorizesBlogInstanceAdmin;

    public function viewAny(?User $user): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function view(?User $user, Category $category): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function create(?User $user): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function update(?User $user, Category $category): bool
    {
        return $this->hasBlogAdminSession($user);
    }

    public function delete(?User $user, Category $category): bool
    {
        return $this->hasBlogAdminSession($user);
    }
}
