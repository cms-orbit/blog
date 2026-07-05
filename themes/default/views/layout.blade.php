<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root { --primary: {{ $blogPrimaryColor ?? '#8b5cf6' }}; }
        body { font-family: Georgia, serif; margin: 0; background: #fafafa; color: #111; }
        .wrap { max-width: 920px; margin: 0 auto; padding: 2rem; }
        header { border-bottom: 2px solid var(--primary); padding-bottom: 1rem; margin-bottom: 2rem; }
        .card { background: #fff; border-radius: .5rem; padding: 1.25rem; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        a { color: var(--primary); }
    </style>
</head>
<body>
<div class="wrap">
<header><a href="{{ route('home') }}">{{ instance_context()?->instance->name ?? __('Blog') }}</a></header>
@yield('content')
</div>
</body>
</html>
