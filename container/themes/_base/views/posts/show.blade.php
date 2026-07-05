@extends('blog-theme-base::layout')

@section('title', $post->title)

@section('content')
    <article class="post-body">
        <p class="meta">
            @if($post->published_at){{ $post->published_at->format('M j, Y') }}@endif
            @if($post->category) · <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>@endif
        </p>
        <h1>{{ $post->title }}</h1>
        @if($post->excerpt)<p><em>{{ $post->excerpt }}</em></p>@endif
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
@endsection
