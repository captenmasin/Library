<!DOCTYPE html>
@php
    $seoMeta = new \Artesaos\SEOTools\Facades\SEOMeta;

    $pageTitle = isset($exception)
            ?  $exception->getStatusCode() . ' ' . $seoMeta::getTitleSeparator() . ' ' . config('app.name')
            : $seoMeta::getTitle();

    $buildId = Vite::manifestHash('build');

    $isPwa = \Illuminate\Support\Facades\Cookie::get('pwa-mode') === 'true';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class([
    'transition-colors duration-300 ease-in-out' => true,
    'dark' => ($appearance ?? 'system') == 'dark',
    'pwa' => \Illuminate\Support\Facades\Cookie::get('pwa-mode') === 'true',
    'pwa-ios' => \Illuminate\Support\Facades\Cookie::get('pwa-device') === 'ios',
    'pwa-android' => \Illuminate\Support\Facades\Cookie::get('pwa-device') === 'android',
    ])
>
<head>
    <meta charset="utf-8">
    @if($isPwa)
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @else
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @endif

    <title>{!! $pageTitle !!}</title>

    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v={{ $buildId }}" />
    <link rel="mask-icon" href="/favicon-mask.svg?v={{ $buildId }}" color="{{ config('pwa.manifest.primary_color') }}">
    <link rel="shortcut icon" href="{{ $appearance === 'dark' ? '/favicon_dark.ico?v='.$buildId : '/favicon.ico?v='.$buildId }}" />
    <link rel="icon" href="/favicon.ico?v={{ $buildId }}" type="image/x-icon" media="(prefers-color-scheme: light)">
    <link rel="icon" href="/favicon_dark.ico?v={{ $buildId }}" type="image/x-icon" media="(prefers-color-scheme: dark)">

    @if(!empty($meta['preload']))
        @foreach($meta['preload'] as $preload)
            @if($preload['href'])
                <link rel="preload" href="{{ $preload['href'] }}" as="{{ $preload['as'] }}" />
            @endif
        @endforeach
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @include('partials.meta.analytics')
    @include('partials.meta.seo')
    @include('partials.meta.pwa')

    <script>
        (function () {
            const appearance = '{{ $appearance ?? "system" }}'

            if (appearance === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

                if (prefersDark) {
                    document.documentElement.classList.add('dark')
                }
            }
        })()
    </script>

    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: #0f0f0f;
        }
    </style>

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-background">
@inertia
</body>
</html>
