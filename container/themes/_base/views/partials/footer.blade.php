<footer class="site-footer">
    <p>&copy; {{ now()->year }} {{ instance_context()?->instance->name ?? config('app.name') }}</p>
</footer>
