@extends('blog-theme-dark::layout')

@section('title', __('Posts'))

@section('content')
    @forelse($posts as $post)
        <article class="entry">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        </article>
    @empty
        <p>{{ __('No posts yet.') }}</p>
    @endforelse
    {{ $posts->links() }}
@endsection
