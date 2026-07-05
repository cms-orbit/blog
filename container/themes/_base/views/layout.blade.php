<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', instance_context()?->instance->name ?? config('app.name'))</title>
    <style>
        :root {
            --primary: {{ $blogPrimaryColor ?? '#8b5cf6' }};
            --surface: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; margin: 0; background: #f8fafc; color: var(--text); line-height: 1.6; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }
        header.site-header { border-bottom: 2px solid var(--primary); padding-bottom: 1rem; margin-bottom: 2rem; }
        nav.site-nav { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: .75rem; font-size: .95rem; }
        nav.site-nav a { color: var(--text); text-decoration: none; }
        nav.site-nav a:hover { color: var(--primary); }
        .hero { margin-bottom: 2rem; }
        .hero h1 { margin: 0 0 .5rem; font-size: 2rem; }
        .hero p { margin: 0; color: var(--muted); }
        .post-grid { display: grid; gap: 1rem; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: .75rem; padding: 1.25rem; }
        .card h2 { margin: 0 0 .5rem; font-size: 1.25rem; }
        .card h2 a { color: inherit; text-decoration: none; }
        .card h2 a:hover { color: var(--primary); }
        .meta { color: var(--muted); font-size: .875rem; margin-bottom: .5rem; }
        .pagination { margin-top: 2rem; }
        article.post-body { background: var(--surface); border: 1px solid var(--border); border-radius: .75rem; padding: 2rem; }
        article.post-body h1 { margin-top: 0; }
        footer.site-footer { margin-top: 3rem; padding-top: 1rem; border-top: 1px solid var(--border); color: var(--muted); font-size: .875rem; }
        @yield('theme_styles')
    </style>
</head>
<body @yield('body_attrs')>
<div class="wrap">
    @include('blog-theme-base::partials.header')
    @yield('content')
    @include('blog-theme-base::partials.footer')
</div>
</body>
</html>
