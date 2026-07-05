@extends('blog-theme-editorial::layout')

@section('title', __('Posts'))

@section('content')
    @php($featured = $posts->first())
    @if($featured)
        <section class="featured">
            <p class="meta">{{ __('Featured') }}</p>
            <h1><a href="{{ route('posts.show', $featured->slug) }}">{{ $featured->title }}</a></h1>
            @if($featured->excerpt)<p>{{ $featured->excerpt }}</p>@endif
        </section>
    @endif
    @foreach($posts->skip(1) as $post)
        <article class="story">
            <p class="meta">@if($post->published_at){{ $post->published_at->format('M j, Y') }}@endif</p>
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        </article>
    @endforeach
    {{ $posts->links() }}
@endsection
