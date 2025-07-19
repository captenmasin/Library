@if(!empty($meta['json']))
    @if(isset($meta['json'][1]))
        @foreach($meta['json'] as $json)
            <script type="application/ld+json">
                {!! json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
            </script>
        @endforeach
    @else
        <script type="application/ld+json">
            {!! json_encode($meta['json'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
@endif
