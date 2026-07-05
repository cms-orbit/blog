<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Screen\Sight;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Saas\Instance\Models\Instance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class BlogInstanceViewScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Blog Instance');
    }

    public function description(): ?string
    {
        return __('Owner details, routing, and quick links for this blog workspace.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(string $id): array
    {
        $instance = $this->findBlogInstance($id);
        $user = Auth::guard(config('orbit.guard', 'web'))->user();
        $publicUrl = $this->publicBlogUrl($instance);
        $adminUrl = $user !== null ? $this->blogAdminUrl($instance, $user) : null;

        return [
            'instance' => [
                'id' => $instance->getKey(),
                'name' => $instance->name,
                'email' => $instance->email ?: '—',
                'lifecycleLabel' => $this->lifecycleLabel($instance->lifecycle),
                'theme' => $instance->theme ?: __('Default'),
                'primaryEndpoint' => $instance->primaryEndpoint()?->normalizedValue() ?? '—',
                'publicUrl' => $publicUrl,
                'adminUrl' => $adminUrl,
                'containerName' => $instance->container
                    ? $this->containerNameLabel($instance->container->name)
                    : '—',
                'entityUrl' => Route::has('orbit.entities.instances.view')
                    ? route('orbit.entities.instances.view', ['id' => $instance->getKey()])
                    : null,
            ],
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        $id = (string) request()->route('id');
        $instance = $this->findBlogInstance($id);
        $publicUrl = $this->publicBlogUrl($instance);
        $adminUrl = $this->blogAdminUrl($instance);

        $actions = [
            Link::make(__('Back to instances'))
                ->icon('bs.arrow-left')
                ->route('orbit.blog.instances.index'),
        ];

        if ($publicUrl !== null) {
            $actions[] = Link::make(__('Open public blog'))
                ->icon('bs.box-arrow-up-right')
                ->target('_blank')
                ->href($publicUrl);
        }

        if ($adminUrl !== null) {
            $actions[] = Link::make(__('Open instance admin'))
                ->icon('bs.shield-lock')
                ->target('_blank')
                ->href($adminUrl);
        }

        return $actions;
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            LayoutFactory::legend('instance', [
                Sight::make('name', __('Name')),
                Sight::make('containerName', __('Container')),
                Sight::make('email', __('Owner')),
                Sight::make('lifecycleLabel', __('Lifecycle')),
                Sight::make('theme', __('Theme')),
                Sight::make('primaryEndpoint', __('Primary Endpoint')),
                Sight::make('publicUrl', __('Public Site')),
                Sight::make('adminUrl', __('Instance Admin')),
            ])->title(__('Instance details')),
        ];
    }
}
