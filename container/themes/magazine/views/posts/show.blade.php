@extends('blog-theme-magazine::layout')

@section('title', $post->title)

@section('content')
    <article class="tile" style="padding:1.5rem;">
        @if($post->category)<span class="chip">{{ $post->category->name }}</span>@endif
        <h1>{{ $post->title }}</h1>
        <div>{!! nl2br(e($post->body)) !!}</div>
    </article>
@endsection
