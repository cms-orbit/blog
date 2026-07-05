@extends('blog-theme-magazine::layout')

@section('title', __('About'))

@section('content')
    <article class="tile" style="padding:1.5rem;"><h1>{{ __('About') }}</h1><p>{{ __('Magazine coverage from :name.', ['name' => instance_context()?->instance->name ?? __('Blog')]) }}</p></article>
@endsection
