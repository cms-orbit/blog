@extends('blog-package::layouts.public')

@section('title', __('Posts'))

@section('content')
    <h1>{{ __('Latest Posts') }}</h1>

    @forelse($posts as $post)
        <article class="card">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            @if($post->excerpt)
                <p>{{ $post->excerpt }}</p>
            @endif
            <p class="meta">
                @if($post->category)
                    {{ $post->category->name }} ·
                @endif
                {{ optional($post->published_at)->format('Y-m-d') }}
            </p>
        </article>
    @empty
        <p>{{ __('No posts yet.') }}</p>
    @endforelse

    {{ $posts->links() }}
@endsection
