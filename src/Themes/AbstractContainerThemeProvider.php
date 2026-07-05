<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Themes;

use Illuminate\Support\ServiceProvider;

abstract class AbstractContainerThemeProvider extends ServiceProvider
{
    abstract public function themeSlug(): string;

    public function boot(): void
    {
        $viewsPath = $this->themeViewsPath();

        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'blog-theme-'.$this->themeSlug());
        }
    }

    protected function themeViewsPath(): string
    {
        return dirname(__DIR__, 2).'/container/themes/'.$this->themeSlug().'/views';
    }
}
