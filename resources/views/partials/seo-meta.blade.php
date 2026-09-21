{{-- Renders from whatever $seo array the calling view passed in (built
     via App\Services\SeoService), falling back to global settings. --}}
@php($seo = $seo ?? app(\App\Services\SeoService::class)->forGlobal())
<title>{{ $seo['title'] }}</title>
@if(!empty($seo['description']))<meta name="description" content="{{ $seo['description'] }}">@endif
@if(!empty($seo['keywords']))<meta name="keywords" content="{{ $seo['keywords'] }}">@endif
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta name="robots" content="{{ $seo['robots'] }}">
<meta property="og:title" content="{{ $seo['og_title'] }}">
@if(!empty($seo['og_description']))<meta property="og:description" content="{{ $seo['og_description'] }}">@endif
@if(!empty($seo['og_image']))<meta property="og:image" content="{{ asset($seo['og_image']) }}">@endif
<meta property="og:type" content="website">
