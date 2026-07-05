@extends('blog-theme-photo::layout')

@section('title', $post->title)

@section('content')
    <article class="shot">
        <div style="background:linear-gradient(135deg, var(--primary), #222);aspect-ratio:16/9;"></div>
        <div class="shot-body">
            <h1>{{ $post->title }}</h1>
            <div>{!! nl2br(e($post->body)) !!}</div>
        </div>
    </article>
@endsection
