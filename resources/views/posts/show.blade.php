@extends('blog-package::layouts.public')

@section('title', $post->meta_title ?: $post->title)

@section('meta')
    @if($post->meta_description)
        <meta name="description" content="{{ $post->meta_description }}">
    @endif
@endsection

@section('content')
    <article class="card">
        <h1>{{ $post->title }}</h1>
        <p class="meta">
            @if($post->category)
                <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a> ·
            @endif
            {{ optional($post->published_at)->format('Y-m-d') }}
        </p>
        @if($post->featured_image)
            <p><img src="{{ $post->featured_image }}" alt="" style="max-width:100%;border-radius:.5rem;"></p>
        @endif
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
    <p><a href="{{ route('home') }}">&larr; {{ __('Back to posts') }}</a></p>
@endsection
