<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', instance_context()?->instance->name ?? config('app.name'))</title>
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#2563eb' }}; }
        body { margin: 0; font-family: system-ui, sans-serif; background: #000; color: #fff; }
        .wrap { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        header { padding: 1rem 0 2rem; }
        .masonry { columns: 3 280px; column-gap: 1rem; }
        .shot { break-inside: avoid; margin-bottom: 1rem; background: #111; border-radius: .75rem; overflow: hidden; }
        .shot img { width: 100%; display: block; aspect-ratio: 4/3; object-fit: cover; background: linear-gradient(135deg, var(--primary), #111); }
        .shot-body { padding: 1rem; }
        .shot h2 { margin: 0; font-size: 1rem; }
        .shot h2 a { color: #fff; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrap">
    <header><strong>{{ instance_context()?->instance->name }}</strong> · <a href="{{ route('home') }}" style="color:#fff;">{{ __('Home') }}</a></header>
    @yield('content')
</div>
</body>
</html>
