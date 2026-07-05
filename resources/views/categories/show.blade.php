@extends('blog-package::layouts.public')

@section('title', $category->name)

@section('content')
    <h1>{{ $category->name }}</h1>
    @if($category->description)
        <p>{{ $category->description }}</p>
    @endif

    @forelse($posts as $post)
        <article class="card">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
            <p class="meta">{{ optional($post->published_at)->format('Y-m-d') }}</p>
        </article>
    @empty
        <p>{{ __('No posts in this category yet.') }}</p>
    @endforelse

    {{ $posts->links() }}
@endsection
