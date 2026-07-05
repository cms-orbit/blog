<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Http\Controllers;

use CmsOrbit\Blog\Support\BlogViewResolver;
use Illuminate\Contracts\View\View;

class PageController
{
    public function about(BlogViewResolver $views): View
    {
        return view($views->resolve('pages.about'));
    }
}
