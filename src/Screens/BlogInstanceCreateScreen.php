<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Screens;

use CmsOrbit\Blog\Screens\Concerns\InteractsWithBlogContainer;
use CmsOrbit\Core\Screen\Action;
use CmsOrbit\Core\Screen\Actions\Button;
use CmsOrbit\Core\Screen\Actions\Link;
use CmsOrbit\Core\Screen\Fields\Input;
use CmsOrbit\Core\Screen\Fields\Select;
use CmsOrbit\Core\Screen\Layout;
use CmsOrbit\Core\Screen\Screen;
use CmsOrbit\Core\Support\Facades\Layout as LayoutFactory;
use CmsOrbit\Core\Support\Facades\Toast;
use CmsOrbit\Saas\Enums\InstanceLifecycle;
use CmsOrbit\Saas\Instance\InstanceProvisioner;
use CmsOrbit\Saas\Instance\Models\RouteEndpoint;
use CmsOrbit\Saas\Theme\ThemeRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogInstanceCreateScreen extends Screen
{
    use InteractsWithBlogContainer;

    public function name(): ?string
    {
        return __('Create Blog Instance');
    }

    public function description(): ?string
    {
        return __('Provision a new blog workspace with routing and a theme.');
    }

    public function permission(): ?iterable
    {
        return ['blog.dashboard'];
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        $container = $this->blogContainer();

        return [
            'instance' => [
                'name' => '',
                'email' => '',
                'subdomain' => '',
                'path' => '',
                'theme' => 'default',
                'lifecycle' => InstanceLifecycle::Active->value,
            ],
            'container' => $container ? [
                'id' => $container->getKey(),
                'name' => $this->containerNameLabel($container->name),
            ] : null,
        ];
    }

    /**
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('Back to instances'))
                ->icon('bs.arrow-left')
                ->route('orbit.blog.instances.index'),

            Button::make(__('Create Blog Instance'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * @return Layout[]
     */
    public function layout(): array
    {
        $themes = $this->blogThemeOptions();

        return [
            LayoutFactory::rows([
                Input::make('instance.name')
                    ->title(__('Name'))
                    ->required()
                    ->placeholder(__('My Company Blog')),
                Input::make('instance.email')
                    ->title(__('Owner email'))
                    ->type('email'),
                Input::make('instance.path')
                    ->title(__('Instance host (path)'))
                    ->help(__('Public path on :host, e.g. acme opens :example.', [
                        'host' => \CmsOrbit\Blog\Support\BlogContainerDomain::host(),
                        'example' => \CmsOrbit\Blog\Support\BlogContainerDomain::host().'/acme/',
                    ])),
                Select::make('instance.theme')
                    ->title(__('Theme'))
                    ->options($themes)
                    ->required()
                    ->help(__('Choose the public blog theme for this workspace.')),
                Select::make('instance.lifecycle')
                    ->title(__('Lifecycle'))
                    ->options(collect(InstanceLifecycle::cases())->mapWithKeys(
                        fn (InstanceLifecycle $case) => [$case->value => $this->lifecycleLabel($case)]
                    )->all()),
            ])->title(__('Blog workspace')),
        ];
    }

    public function save(Request $request): RedirectResponse
    {
        $container = $this->blogContainer();

        abort_if($container === null, 404);

        $validated = $request->validate([
            'instance.name' => ['required', 'string', 'max:255'],
            'instance.email' => ['nullable', 'email', 'max:255'],
            'instance.path' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new RouteEndpoint)->getTable(), 'value'),
            ],
            'instance.theme' => ['required', 'string', Rule::in(array_keys($this->blogThemeOptions()))],
            'instance.lifecycle' => ['required', Rule::enum(InstanceLifecycle::class)],
        ], [], [
            'instance.name' => __('Name'),
            'instance.email' => __('Owner email'),
            'instance.path' => __('Path prefix'),
            'instance.theme' => __('Theme'),
            'instance.lifecycle' => __('Lifecycle'),
        ]);

        /** @var array<string, mixed> $payload */
        $payload = $validated['instance'];

        if (blank($payload['path'] ?? null)) {
            return redirect()->back()->withErrors([
                'instance.path' => __('Provide a path prefix for the blog instance.'),
            ]);
        }

        try {
            app(InstanceProvisioner::class)->create(
                container: $container,
                name: (string) $payload['name'],
                path: (string) $payload['path'],
                theme: (string) $payload['theme'],
                data: array_filter([
                    'email' => $payload['email'] ?? null,
                ]),
            );
        } catch (\Throwable $exception) {
            Toast::error($exception->getMessage());

            return redirect()->back()->withInput();
        }

        Toast::success(__('The blog instance was created.'));

        return redirect()->route('orbit.blog.instances.index');
    }

    /**
     * @return array<string, string>
     */
    protected function blogThemeOptions(): array
    {
        return collect(app(ThemeRegistry::class)->forContainer('blog'))
            ->mapWithKeys(fn ($registration, string $name) => [$name => str($name)->headline()->toString()])
            ->all();
    }
}
