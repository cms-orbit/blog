{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0">
<channel>
    <title>{{ instance_context()?->instance->name ?? config('app.name') }}</title>
    <link>{{ url('/') }}</link>
    <description>{{ __('Blog feed') }}</description>
    @foreach($posts as $post)
    <item>
        <title>{{ $post->title }}</title>
        <link>{{ route('posts.show', $post->slug) }}</link>
        <guid>{{ route('posts.show', $post->slug) }}</guid>
        <pubDate>{{ optional($post->published_at)->toRfc2822String() }}</pubDate>
        <description><![CDATA[{!! $post->excerpt ?? Str::limit($post->body, 200) !!}]]></description>
    </item>
    @endforeach
</channel>
</rss>
