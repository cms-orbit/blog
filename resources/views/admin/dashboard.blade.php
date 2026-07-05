<div class="space-y-4">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Total Posts') }}</p>
            <p class="text-2xl font-semibold">{{ $stats['posts'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">{{ __('Published') }}</p>
            <p class="text-2xl font-semibold">{{ $stats['published'] ?? 0 }}</p>
        </div>
    </div>

    <div class="rounded-xl border bg-white p-4 shadow-sm">
        <h3 class="mb-3 text-lg font-semibold">{{ __('Recent Posts') }}</h3>
        <ul class="divide-y">
            @forelse(($stats['recent'] ?? []) as $post)
                <li class="py-2 flex justify-between gap-4">
                    <span>{{ $post->title }}</span>
                    <span class="text-sm text-gray-500">{{ optional($post->updated_at)->diffForHumans() }}</span>
                </li>
            @empty
                <li class="py-2 text-gray-500">{{ __('No posts yet.') }}</li>
            @endforelse
        </ul>
    </div>
</div>
