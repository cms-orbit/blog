@extends('blog-theme-default::layout')

@section('title', $post->meta_title ?: $post->title)

@section('content')
    <article class="card">
        <h1>{{ $post->title }}</h1>
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
@endsection
