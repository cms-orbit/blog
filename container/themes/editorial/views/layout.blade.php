<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', instance_context()?->instance->name ?? config('app.name'))</title>
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#7c3aed' }}; --ink: #1f2937; --paper: #fffdf8; }
        body { margin: 0; font-family: Georgia, 'Times New Roman', serif; background: var(--paper); color: var(--ink); }
        .wrap { max-width: 760px; margin: 0 auto; padding: 2.5rem 1.5rem 4rem; }
        header { border-bottom: 1px solid #ddd; margin-bottom: 2rem; padding-bottom: 1rem; }
        nav a { margin-right: 1rem; color: var(--ink); text-decoration: none; font-size: .9rem; text-transform: uppercase; letter-spacing: .08em; }
        .featured { border-left: 4px solid var(--primary); padding-left: 1rem; margin-bottom: 2rem; }
        .story { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #ececec; }
        .story h2 { margin: 0 0 .5rem; font-size: 1.75rem; }
        .story h2 a { color: inherit; text-decoration: none; }
        .meta { color: #6b7280; font-size: .875rem; }
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
