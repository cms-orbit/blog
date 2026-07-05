@extends('blog-theme-photo::layout')

@section('title', __('Posts'))

@section('content')
    <div class="masonry">
        @forelse($posts as $post)
            <article class="shot">
                <div style="background:linear-gradient(135deg, var(--primary), #222);aspect-ratio:4/3;"></div>
                <div class="shot-body">
                    <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
                    @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
                </div>
            </article>
        @empty
            <p>{{ __('No posts yet.') }}</p>
        @endforelse
    </div>
    {{ $posts->links() }}
@endsection
