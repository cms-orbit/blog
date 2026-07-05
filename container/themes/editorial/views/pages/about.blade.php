@extends('blog-theme-editorial::layout')

@section('title', __('About'))

@section('content')
    <article class="story">
        <h1>{{ __('About') }}</h1>
        <p>{{ __('Editorial stories from :name.', ['name' => instance_context()?->instance->name ?? __('Blog')]) }}</p>
    </article>
@endsection
