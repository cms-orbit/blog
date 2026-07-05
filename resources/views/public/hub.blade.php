<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Blog Hub') }}</title>
    <style>
        :root { --primary: #8b5cf6; }
        body { font-family: system-ui, sans-serif; margin: 0; background: #fafafa; color: #111; }
        .wrap { max-width: 960px; margin: 0 auto; padding: 2rem; }
        header { border-bottom: 3px solid var(--primary); padding-bottom: 1rem; margin-bottom: 2rem; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem; margin-bottom: 1rem; }
        .cta { display: inline-block; margin-top: 1rem; padding: .75rem 1.25rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: .5rem; }
        .grid { display: grid; gap: 1rem; }
        .meta { color: #6b7280; font-size: .875rem; margin-top: .35rem; }
        .badge { display: inline-block; background: #ede9fe; color: #5b21b6; padding: .15rem .5rem; border-radius: 999px; font-size: .75rem; margin-right: .35rem; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>{{ __('Blog Hub') }}</h1>
        <p>{{ __('Create your blog on :host or explore published workspaces.', ['host' => $blogHost ?? 'blog']) }}</p>
    </header>

    @if ($createUrl)
        <div class="card">
            <h2>{{ __('Create a new blog') }}</h2>
            <p>{{ __('Provision a blog workspace with your own theme and URL path on :host.', ['host' => $blogHost ?? 'blog']) }}</p>
            <a class="cta" href="{{ $createUrl }}">{{ __('Create blog instance') }}</a>
        </div>
    @endif

    @if (! empty($instances))
        <section class="grid">
            <h2>{{ __('Published blogs') }}</h2>
            @foreach ($instances as $instance)
                <article class="card">
                    <h3><a href="{{ $instance['url'] }}">{{ $instance['name'] }}</a></h3>
                    <p class="meta">
                        <span class="badge">{{ $instance['path'] }}</span>
                        <span class="badge">{{ __('Theme') }}: {{ $instance['theme'] }}</span>
                        <span class="badge">{{ __('Posts') }}: {{ number_format((int) ($instance['publishedPosts'] ?? 0)) }}</span>
                    </p>
                </article>
            @endforeach
        </section>
    @else
        <p>{{ __('No blog instances have been published yet.') }}</p>
    @endif
</div>
</body>
</html>
