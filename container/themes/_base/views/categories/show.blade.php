@extends('blog-theme-base::layout')

@section('title', $category->name)

@section('content')
    <section class="hero">
        <h1>{{ $category->name }}</h1>
        @if($category->description)<p>{{ $category->description }}</p>@endif
    </section>
    <div class="post-grid">
        @forelse($posts as $post)
            @include('blog-theme-base::partials.post-card', ['post' => $post])
        @empty
            <p>{{ __('No posts in this category yet.') }}</p>
        @endforelse
    </div>
    <div class="pagination">{{ $posts->links() }}</div>
@endsection
