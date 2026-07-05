<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Support;

use CmsOrbit\Blog\Models\BlogSetting;
use CmsOrbit\Saas\Container\ContainerManager;
use CmsOrbit\Saas\Instance\Models\Instance;

class BlogContainerConfig
{
    public function adminPrefix(): string
    {
        return $this->definition()?->admin->prefix ?? 'admin';
    }

    public function ssoTtlMinutes(): int
    {
        return $this->definition()?->admin->ssoTtlMinutes ?? 30;
    }

    public function themeSettingsDefaults(?Instance $instance = null): array
    {
        $theme = $instance?->theme ?? instance_context()?->instance->theme ?? 'default';
        $containerThemesPath = dirname(__DIR__, 2)."/container/themes/{$theme}/config/settings.php";

        if (is_file($containerThemesPath)) {
            /** @var array<string, mixed> $settings */
            $settings = require $containerThemesPath;

            return $settings;
        }

        $legacyPath = base_path("themes/blog/{$theme}/config/settings.php");

        if (is_file($legacyPath)) {
            /** @var array<string, mixed> $settings */
            $settings = require $legacyPath;

            return $settings;
        }

        $packagePath = dirname(__DIR__, 2).'/container/themes/default/config/settings.php';

        if (is_file($packagePath)) {
            /** @var array<string, mixed> $settings */
            $settings = require $packagePath;

            return $settings;
        }

        return [
            'primary_color' => '#8b5cf6',
            'logo_url' => '',
            'posts_per_page' => 12,
        ];
    }

    public function themeSetting(string $key, mixed $default = null): mixed
    {
        if ($this->shouldReadStoredThemeSettings()) {
            $stored = BlogSetting::getValue('theme.'.$key);

            if ($stored !== null) {
                return is_array($stored) && array_key_exists('value', $stored)
                    ? $stored['value']
                    : $stored;
            }
        }

        $defaults = $this->themeSettingsDefaults();

        return $defaults[$key] ?? $default;
    }

    protected function shouldReadStoredThemeSettings(): bool
    {
        if (instance_context() !== null) {
            return true;
        }

        $runtime = saas();

        return $runtime->initialized && $runtime->instance !== null;
    }

    protected function definition()
    {
        return app(ContainerManager::class)->get('blog');
    }
}
