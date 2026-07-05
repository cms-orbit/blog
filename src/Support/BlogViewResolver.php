<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Support;

class BlogViewResolver
{
    public function resolve(string $view): string
    {
        $theme = instance_context()?->instance->theme ?? 'default';
        $themeView = "blog-theme-{$theme}::{$view}";

        if (view()->exists($themeView) && $this->viewRendersContent($themeView, $view)) {
            return $themeView;
        }

        $baseView = "blog-theme-base::{$view}";

        if (view()->exists($baseView)) {
            return $baseView;
        }

        return "blog-package::{$view}";
    }

    protected function viewRendersContent(string $themeView, string $view): bool
    {
        if (! in_array($view, ['posts.index', 'posts.show', 'categories.show', 'pages.about'], true)) {
            return true;
        }

        $path = view()->getFinder()->find($themeView);

        if (! is_string($path) || ! is_readable($path)) {
            return false;
        }

        $contents = (string) file_get_contents($path);

        if ($view === 'posts.index') {
            return str_contains($contents, '$posts');
        }

        if ($view === 'posts.show') {
            return str_contains($contents, '$post');
        }

        return str_contains($contents, '@section');
    }
}
