@extends('blog-theme-base::layout')

@section('title', __('Posts'))

@section('content')
    <section class="hero">
        <h1>{{ __('Latest Posts') }}</h1>
        <p>{{ instance_context()?->instance->name }}</p>
    </section>
    <div class="post-grid">
        @forelse($posts as $post)
            @include('blog-theme-base::partials.post-card', ['post' => $post])
        @empty
            <p>{{ __('No posts yet.') }}</p>
        @endforelse
    </div>
    <div class="pagination">{{ $posts->links() }}</div>
@endsection
