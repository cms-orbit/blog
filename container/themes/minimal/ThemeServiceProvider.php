<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Themes\Minimal;

use CmsOrbit\Blog\Themes\AbstractContainerThemeProvider;

class ThemeServiceProvider extends AbstractContainerThemeProvider
{
    public function themeSlug(): string
    {
        return 'minimal';
    }
}
