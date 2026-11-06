@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'schema' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'noindex' => false,
])

@php
    $resolvedTitle = \App\Services\SeoService::formatTitle($title);
    $resolvedDescription = \App\Services\SeoService::cleanDescription($description);
    $resolvedCanonical = \App\Services\SeoService::canonicalUrl($canonical);
    $resolvedOgType = $ogType ?: 'website';
    $resolvedOgImage = \App\Services\SeoService::resolveOgImage($ogImage);
@endphp

<title>{{ $resolvedTitle }}</title>
<meta name="description" content="{{ $resolvedDescription }}">
@if($noindex)
<meta name="robots" content="noindex, nofollow">
@endif
@if(!empty($keywords))
<meta name="keywords" content="{{ is_array($keywords) ? implode(', ', $keywords) : $keywords }}">
@endif
<link rel="canonical" href="{{ $resolvedCanonical }}">
<link rel="icon" type="image/png" href="{{ asset('images/aptimedha-logo.png') }}">

{{-- Open Graph Protocol Metadata --}}
<meta property="og:site_name" content="AptiMedha">
<meta property="og:type" content="{{ $resolvedOgType }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
<meta property="og:description" content="{{ $resolvedDescription }}">
<meta property="og:url" content="{{ $resolvedCanonical }}">
<meta property="og:image" content="{{ $resolvedOgImage }}">
<meta property="og:locale" content="en_US">

{{-- Twitter Card Metadata --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resolvedTitle }}">
<meta name="twitter:description" content="{{ $resolvedDescription }}">
<meta name="twitter:image" content="{{ $resolvedOgImage }}">

@if(!empty($schema))
<script type="application/ld+json">
{!! \App\Services\SchemaService::toJsonLd($schema) !!}
</script>
@endif

