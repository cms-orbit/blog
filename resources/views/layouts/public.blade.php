<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @yield('meta')
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#8b5cf6' }}; }
        body { font-family: system-ui, sans-serif; margin: 0; color: #1f2937; background: #f9fafb; }
        header { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; align-items: center; gap: 1rem; }
        header a { color: inherit; text-decoration: none; font-weight: 700; }
        main { max-width: 960px; margin: 0 auto; padding: 2rem; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem; margin-bottom: 1rem; }
        .card h2 { margin: 0 0 .5rem; font-size: 1.25rem; }
        .card h2 a { color: inherit; text-decoration: none; }
        .meta { color: #6b7280; font-size: .875rem; }
        footer { text-align: center; padding: 2rem; color: #6b7280; font-size: .875rem; }
        .btn { display: inline-block; background: var(--primary); color: #fff; padding: .5rem 1rem; border-radius: .5rem; text-decoration: none; }
    </style>
    @stack('styles')
</head>
<body>
<header>
    @if(!empty($blogLogoUrl))
        <img src="{{ $blogLogoUrl }}" alt="" height="32">
    @endif
    <a href="{{ route('home') }}">{{ instance_context()?->instance->name ?? __('Blog') }}</a>
</header>
<main>
    @yield('content')
</main>
<footer>&copy; {{ date('Y') }} {{ instance_context()?->instance->name ?? config('app.name') }}</footer>
@stack('scripts')
</body>
</html>
