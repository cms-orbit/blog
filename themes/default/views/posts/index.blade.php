@extends('blog-theme-default::layout')

@section('title', __('Posts'))

@section('content')
    <h1>{{ __('Latest Posts') }}</h1>
    @forelse($posts as $post)
        <article class="card">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        </article>
    @empty
        <p>{{ __('No posts yet.') }}</p>
    @endforelse
    {{ $posts->links() }}
@endsection
