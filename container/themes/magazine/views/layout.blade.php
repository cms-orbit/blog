<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', instance_context()?->instance->name ?? config('app.name'))</title>
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#dc2626' }}; }
        body { margin: 0; font-family: 'Helvetica Neue', Arial, sans-serif; background: #111; color: #f5f5f5; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 3rem; }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid var(--primary); padding-bottom: 1rem; margin-bottom: 2rem; }
        nav a { color: #f5f5f5; margin-left: 1rem; text-decoration: none; font-weight: 700; text-transform: uppercase; font-size: .75rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        .tile { background: #1f1f1f; border-radius: .5rem; overflow: hidden; }
        .tile-body { padding: 1rem; }
        .tile h2 { margin: 0 0 .5rem; font-size: 1.1rem; }
        .tile h2 a { color: #fff; text-decoration: none; }
        .chip { display: inline-block; background: var(--primary); color: #fff; padding: .15rem .5rem; border-radius: 999px; font-size: .7rem; margin-bottom: .5rem; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <strong>{{ instance_context()?->instance->name }}</strong>
        <nav>
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <a href="{{ route('about') }}">{{ __('About') }}</a>
        </nav>
    </header>
    @yield('content')
</div>
</body>
</html>
