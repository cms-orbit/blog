<article class="card">
    <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
    @if($post->published_at)
        <p class="meta">{{ $post->published_at->format('M j, Y') }}@if($post->category) · {{ $post->category->name }}@endif</p>
    @endif
    @if($post->excerpt)
        <p>{{ $post->excerpt }}</p>
    @endif
</article>
