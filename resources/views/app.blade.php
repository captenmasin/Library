<!DOCTYPE html>
@php
    $seoMeta = new \Artesaos\SEOTools\Facades\SEOMeta;

    $pageTitle = isset($exception)
            ?  $exception->getStatusCode() . ' ' . $seoMeta::getTitleSeparator() . ' ' . config('app.name')
            : $seoMeta::getTitle();

    $buildId = Vite::manifestHash('build');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class([
    'dark' => ($appearance ?? 'system') == 'dark',
    'pwa' => \Illuminate\Support\Facades\Cookie::get('pwa-mode') === 'true',
    'pwa-ios' => \Illuminate\Support\Facades\Cookie::get('pwa-device') === 'ios',
    'pwa-android' => \Illuminate\Support\Facades\Cookie::get('pwa-device') === 'android',
    ])
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{!! $pageTitle !!}</title>

    <link rel="icon" type="image/png" href="/favicon-96x96.png?v={{ $buildId }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v={{ $buildId }}" />
    <link rel="shortcut icon" href="/favicon.ico?v={{ $buildId }}" />
    <link rel="mask-icon" href="/favicon-mask.svg?v={{ $buildId }}" color="{{ config('pwa.manifest.primary_color') }}">

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
