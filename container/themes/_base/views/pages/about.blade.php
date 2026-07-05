@extends('blog-theme-base::layout')

@section('title', __('About'))

@section('content')
    <article class="post-body">
        <h1>{{ __('About') }}</h1>
        <p>{{ __('Welcome to :name. This workspace is powered by the Orbit blog container.', ['name' => instance_context()?->instance->name ?? __('Blog')]) }}</p>
    </article>
@endsection
