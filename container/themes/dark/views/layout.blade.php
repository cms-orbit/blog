<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', instance_context()?->instance->name ?? config('app.name'))</title>
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#22d3ee' }}; --bg: #0f172a; --text: #e2e8f0; }
        body { margin: 0; font-family: 'SF Mono', Menlo, monospace; background: var(--bg); color: var(--text); }
        .wrap { max-width: 820px; margin: 0 auto; padding: 2rem 1rem 3rem; }
        header { border: 1px solid #334155; padding: 1rem; margin-bottom: 2rem; border-radius: .5rem; box-shadow: 0 0 20px color-mix(in srgb, var(--primary) 35%, transparent); }
        a { color: var(--primary); }
        .entry { border-left: 2px solid var(--primary); padding-left: 1rem; margin-bottom: 1.5rem; }
        .entry h2 { margin: 0 0 .35rem; font-size: 1.1rem; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <strong>{{ instance_context()?->instance->name }}</strong>
        <div><a href="{{ route('home') }}">{{ __('Home') }}</a> · <a href="{{ route('about') }}">{{ __('About') }}</a></div>
    </header>
    @yield('content')
</div>
</body>
</html>
