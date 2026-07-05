@php($container = $hub['container'] ?? null)
@php($metrics = $hub['metrics'] ?? [])
@php($links = $hub['links'] ?? [])
@php($instances = $hub['instances'] ?? [])
@php($endpoints = $hub['endpoints'] ?? [])

<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-violet-50 via-white to-sky-50 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900">
        <div class="space-y-5 px-6 py-6 md:px-8 md:py-8">
            <div class="space-y-3">
                <div class="inline-flex items-center rounded-full border border-violet-200 bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-violet-700 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-200">
                    {{ __('Blog Management') }}
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-slate-50">
                        {{ $container['name'] ?? __('Blog') }}
                    </h2>
                    <p class="max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                        {{ __('Operate the blog package as a dedicated workspace for SaaS instances, themes, and routing.') }}
                    </p>
                </div>
            </div>

            @if ($container)
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        {{ $container['slug'] }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        {{ $container['isolationLabel'] }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        {{ $container['lifecycleLabel'] }}
                    </span>
                    @foreach ($container['routingSupports'] as $support)
                        <span class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-medium text-violet-700 dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-200">
                            {{ $support }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100">
                    {{ __('No synced blog container was found yet. Connect the package first, then revisit this workspace.') }}
                </div>
            @endif
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Blog Instances') }}</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-slate-50">{{ number_format((int) ($metrics['instances'] ?? 0)) }}</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Connected workspaces using the blog container.') }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Active Instances') }}</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-slate-50">{{ number_format((int) ($metrics['activeInstances'] ?? 0)) }}</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Instances currently available to editors and visitors.') }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Domains & Endpoints') }}</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-slate-50">{{ number_format((int) ($metrics['endpoints'] ?? 0)) }}</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Registered domains, subdomains, and paths for blog instances.') }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Themes') }}</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-slate-50">{{ number_format((int) ($metrics['themes'] ?? 0)) }}</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Theme registrations currently available to the blog container.') }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        @foreach ($links as $link)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
                <div class="space-y-3">
                    <div>
                        <h3 class="text-base font-semibold text-slate-950 dark:text-slate-50">{{ $link['title'] }}</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $link['description'] }}</p>
                    </div>

                    <a
                        href="{{ $link['url'] }}"
                        class="inline-flex items-center rounded-lg bg-violet-600 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-violet-500"
                    >
                        {{ $link['cta'] }}
                    </a>
                </div>
            </article>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.4fr_1fr]">
        <article id="instances" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-semibold text-slate-950 dark:text-slate-50">{{ __('Blog Instances') }}</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ __('Recent blog workspaces and their primary publishing endpoints.') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-900/70">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ __('Name') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ __('Lifecycle') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ __('Theme') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ __('Primary Endpoint') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ __('Admin') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($instances as $instance)
                            <tr>
                                <td class="px-5 py-4 text-sm font-medium text-slate-950 dark:text-slate-50">
                                    @if ($instance['url'])
                                        <a href="{{ $instance['url'] }}" class="hover:text-violet-600 dark:hover:text-violet-300">{{ $instance['name'] }}</a>
                                    @else
                                        {{ $instance['name'] }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $instance['lifecycleLabel'] }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $instance['theme'] }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $instance['primaryEndpoint'] }}</td>
                                <td class="px-5 py-4 text-sm">
                                    @if ($instance['adminUrl'] ?? null)
                                        <a href="{{ $instance['adminUrl'] }}" class="font-medium text-violet-600 hover:text-violet-500 dark:text-violet-300">{{ __('Manage blog') }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400">{{ __('No blog instances have been provisioned yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article id="routing" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-semibold text-slate-950 dark:text-slate-50">{{ __('Routing Overview') }}</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ __('Domains, subdomains, and path routes currently attached to blog instances.') }}</p>
            </div>
            <div class="space-y-3 px-5 py-4">
                @forelse ($endpoints as $endpoint)
                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-950 dark:text-slate-50">{{ $endpoint['value'] }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $endpoint['instanceName'] }} · {{ $endpoint['typeLabel'] }}</p>
                            </div>
                            <div class="flex flex-wrap justify-end gap-2">
                                @if ($endpoint['primary'])
                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">{{ __('Primary') }}</span>
                                @endif
                                @if ($endpoint['fallback'])
                                    <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">{{ __('Fallback') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('No routing endpoints are attached to blog instances yet.') }}</p>
                @endforelse
            </div>
        </article>
    </section>

    @if (!empty($hub['themes'] ?? []))
        <section id="themes" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-base font-semibold text-slate-950 dark:text-slate-50">{{ __('Theme Gallery') }}</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ __('Available themes for blog instances.') }}</p>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ($hub['themes'] as $theme)
                    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="font-semibold text-slate-950 dark:text-slate-50">{{ $theme['label'] }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $theme['name'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if (!empty($container['defaultEndpoint'] ?? null))
        <section class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-500/20 dark:bg-violet-500/10">
            <h3 class="text-base font-semibold text-violet-900 dark:text-violet-100">{{ __('Default Blog Endpoint') }}</h3>
            <p class="mt-1 text-sm text-violet-800 dark:text-violet-200">{{ $container['defaultEndpoint'] }}</p>
        </section>
    @endif

    <section id="container" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-2">
                <h3 class="text-base font-semibold text-slate-950 dark:text-slate-50">{{ __('Container Details') }}</h3>
                <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('Inspect container capabilities, routing support, and theme options.') }}</p>
            </div>

            @if ($container)
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $container['name'] }}</span>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $container['slug'] }}</span>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $container['isolationLabel'] }}</span>
                </div>
            @endif
        </div>
    </section>
</div>
