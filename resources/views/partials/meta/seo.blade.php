{!! (new \App\Support\Robots())->metaTag() !!}

<link rel="canonical" href="{!! \Artesaos\SEOTools\Facades\SEOMeta::getCanonical() !!}" />
<meta name="description" content="{!! \Artesaos\SEOTools\Facades\SEOMeta::getDescription() !!}" />

{!! OpenGraph::generate() !!}
{!! Twitter::generate() !!}
<meta name="twitter:domain" content="{{ config('site.domain') }}" />
<meta name="twitter:url" content="{{ \Artesaos\SEOTools\Facades\SEOMeta::getCanonical() }}" />
{!! JsonLd::generate() !!}

@include('partials.meta.json-ld')
