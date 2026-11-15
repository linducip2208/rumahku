@props([
    'title' => config('app.name', 'Rumah123'),
    'description' => 'Rumah123 — Portal properti Indonesia: jual, sewa, proyek baru.',
    'canonical' => null,
    'image' => asset('og-cover.png'),
])

{{-- <title> dirender layout (metronic.blade.php) — komponen ini hanya meta/OG/canonical. --}}
<meta name="description" content="{{ $description }}" />
@if ($canonical)
<link rel="canonical" href="{{ $canonical }}" />
<meta property="og:url" content="{{ $canonical }}" />
@endif
<meta property="og:type" content="website" />
<meta property="og:site_name" content="{{ config('app.name', 'Rumah123') }}" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
@if ($image)
<meta property="og:image" content="{{ $image }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
@if ($image)
<meta name="twitter:image" content="{{ $image }}" />
@endif
