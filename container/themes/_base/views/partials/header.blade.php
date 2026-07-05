<header class="site-header">
    <strong><a href="{{ route('home') }}">{{ instance_context()?->instance->name ?? __('Blog') }}</a></strong>
    @include('blog-theme-base::partials.nav')
</header>
