@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'schema' => null,
])

@php
    $resolvedTitle = \App\Services\SeoService::formatTitle($title);
    $resolvedDescription = \App\Services\SeoService::cleanDescription($description);
    $resolvedCanonical = $canonical ?: url()->current();
@endphp

<title>{{ $resolvedTitle }}</title>
<meta name="description" content="{{ $resolvedDescription }}">
@if(!empty($keywords))
<meta name="keywords" content="{{ is_array($keywords) ? implode(', ', $keywords) : $keywords }}">
@endif
<link rel="canonical" href="{{ $resolvedCanonical }}">
<link rel="icon" type="image/png" href="{{ asset('images/aptimedha-logo.png') }}">
@if(!empty($schema))
<script type="application/ld+json">
{!! \App\Services\SchemaService::toJsonLd($schema) !!}
</script>
@endif

