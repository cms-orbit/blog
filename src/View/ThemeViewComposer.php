<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\View;

use CmsOrbit\Blog\Models\Category;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use CmsOrbit\Blog\Support\BlogContainerDomain;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewContract;

class ThemeViewComposer
{
    public function __construct(protected BlogContainerConfig $config) {}

    public function compose(ViewContract $view): void
    {
        $context = instance_context();

        $view->with([
            'blogTheme' => $context?->instance->theme ?? 'default',
            'blogThemeSettings' => $this->config->themeSettingsDefaults($context?->instance),
            'blogPrimaryColor' => $this->config->themeSetting('primary_color', '#8b5cf6'),
            'blogLogoUrl' => $this->config->themeSetting('logo_url', ''),
            'blogCategories' => $this->resolveCategories(),
            'blogHubUrl' => 'http://'.BlogContainerDomain::host().'/',
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Category>|null
     */
    protected function resolveCategories()
    {
        if (instance_context() === null) {
            return null;
        }

        try {
            return Category::query()->orderBy('name')->limit(8)->get();
        } catch (\Throwable) {
            return null;
        }
    }
}
