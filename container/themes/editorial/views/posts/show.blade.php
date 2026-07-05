@extends('blog-theme-editorial::layout')

@section('title', $post->title)

@section('content')
    <article class="story">
        <p class="meta">@if($post->published_at){{ $post->published_at->format('F j, Y') }}@endif @if($post->category) · {{ $post->category->name }}@endif</p>
        <h1>{{ $post->title }}</h1>
        @if($post->excerpt)<p><em>{{ $post->excerpt }}</em></p>@endif
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
@endsection
