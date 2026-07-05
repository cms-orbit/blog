<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Models\BlogSetting;
use CmsOrbit\Blog\Support\BlogContainerConfig;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Button;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Layouts\Rows;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ThemeSettingsScreen extends Screen
{
    public function name(): ?string
    {
        return __('Theme Settings');
    }

    public function description(): ?string
    {
        return __('Customize the public appearance of this blog instance.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(BlogContainerConfig $config): array
    {
        $defaults = $config->themeSettingsDefaults();
        $values = [];

        foreach (array_keys($defaults) as $key) {
            $values[$key] = $config->themeSetting($key, $defaults[$key]);
        }

        return [
            'settings' => $values,
            'defaults' => $defaults,
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('View Site'))
                ->icon('bs.box-arrow-up-right')
                ->target('_blank')
                ->href('/'),
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),
            Button::make(__('Reset to defaults'))
                ->icon('bs.arrow-counterclockwise')
                ->method('resetDefaults')
                ->confirm(__('Reset all theme settings to their defaults?')),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::block([
                Rows::make([
                    Input::make('settings.primary_color')->title(__('Primary Color')),
                    Input::make('settings.logo_url')->title(__('Logo URL')),
                    Input::make('settings.posts_per_page')->title(__('Posts Per Page'))->type('number'),
                ]),
            ])->title(__('Theme Settings')),
        ];
    }

    public function save(Request $request, BlogContainerConfig $config): void
    {
        $settings = (array) $request->input('settings', []);

        foreach ($settings as $key => $value) {
            BlogSetting::setValue('theme.'.$key, $value);
        }
    }

    public function resetDefaults(BlogContainerConfig $config): void
    {
        BlogSetting::query()->where('key', 'like', 'theme.%')->delete();
    }
}
