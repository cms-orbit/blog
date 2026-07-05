@extends('blog-theme-magazine::layout')

@section('title', $category->name)

@section('content')
    <h1>{{ $category->name }}</h1>
    <div class="grid">
        @forelse($posts as $post)
            <article class="tile"><div class="tile-body"><h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2></div></article>
        @empty
            <p>{{ __('No posts in this category yet.') }}</p>
        @endforelse
    </div>
    {{ $posts->links() }}
@endsection
