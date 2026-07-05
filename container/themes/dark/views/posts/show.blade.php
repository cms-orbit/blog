@extends('blog-theme-dark::layout')

@section('title', $post->title)

@section('content')
    <article class="entry">
        <h1>{{ $post->title }}</h1>
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
@endsection
