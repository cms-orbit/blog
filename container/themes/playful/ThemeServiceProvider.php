<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Themes\Playful;

use CmsOrbit\Blog\Themes\AbstractContainerThemeProvider;

class ThemeServiceProvider extends AbstractContainerThemeProvider
{
    public function themeSlug(): string
    {
        return 'playful';
    }
}
