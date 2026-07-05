<nav class="site-nav">
    <a href="{{ route('home') }}">{{ __('Home') }}</a>
    <a href="{{ route('about') }}">{{ __('About') }}</a>
    @if(!empty($blogCategories))
        @foreach($blogCategories as $category)
            <a href="{{ route('categories.show', $category->slug) }}">{{ $category->name }}</a>
        @endforeach
    @endif
</nav>
