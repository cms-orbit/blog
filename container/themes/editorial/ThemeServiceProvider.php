<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Themes\Editorial;

use CmsOrbit\Blog\Themes\AbstractContainerThemeProvider;

class ThemeServiceProvider extends AbstractContainerThemeProvider
{
    public function themeSlug(): string
    {
        return 'editorial';
    }
}
