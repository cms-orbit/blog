@extends('blog-theme-editorial::layout')

@section('title', $category->name)

@section('content')
    <h1>{{ $category->name }}</h1>
    @forelse($posts as $post)
        <article class="story">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        </article>
    @empty
        <p>{{ __('No posts in this category yet.') }}</p>
    @endforelse
    {{ $posts->links() }}
@endsection
