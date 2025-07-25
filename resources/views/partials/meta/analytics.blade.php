@if(config('services.pirsch.enabled'))
    {{-- Pirsch --}}
    <link rel="preconnect" href="https://api.pirsch.io">

        <script defer src="https://a.bookbound.app/pa.js"
                id="pianjs"
                data-code="{{ config('services.pirsch.site_id') }}"
                @env('local')data-dev="{{ str_replace('https://', '', config('app.url')) }}"@endenv
                data-hit-endpoint="https://a.bookbound.app/hit"
                data-event-endpoint="https://a.bookbound.app/event"
                data-session-endpoint="https://a.bookbound.app/session"></script>

    </script>
@endif
