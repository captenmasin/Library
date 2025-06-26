<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
@php
    $seoMeta = new \Artesaos\SEOTools\Facades\SEOMeta;
    $pageTitle = isset($exception)
    ?  $exception->getStatusCode() . ' ' . $seoMeta::getTitleSeparator() . ' ' . config('app.name')
    : $seoMeta::getTitle();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? "system" }}';

            if (appearance === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>

    <title>{!! $pageTitle !!}</title>

    <link rel="icon" href="/favicon.ico?v=3" sizes="any">
    <link rel="icon" href="/favicon.svg?v=3" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=3">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    @if(!empty($meta['preload']))
        @foreach($meta['preload'] as $preload)
            @if($preload['href'])
                <link rel="preload" href="{{ $preload['href'] }}" as="{{ $preload['as'] }}" />
            @endif
        @endforeach
    @endif


    <link rel="canonical" href="{!! \Artesaos\SEOTools\Facades\SEOMeta::getCanonical() !!}" />
    <meta name="description" content="{!! \Artesaos\SEOTools\Facades\SEOMeta::getDescription() !!}" />

    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <meta name="twitter:domain" content="{{ config('site.domain') }}" />
    <meta name="twitter:url" content="{{ \Artesaos\SEOTools\Facades\SEOMeta::getCanonical() }}" />
    {!! JsonLd::generate() !!}
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


    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-background">
@inertia
</body>
</html>
