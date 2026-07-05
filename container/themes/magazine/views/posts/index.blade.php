@extends('blog-theme-magazine::layout')

@section('title', __('Posts'))

@section('content')
    <div class="grid">
        @forelse($posts as $post)
            <article class="tile">
                <div class="tile-body">
                    @if($post->category)<span class="chip">{{ $post->category->name }}</span>@endif
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
