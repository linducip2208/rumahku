@extends('layouts.metronic')

@section('title', $title.' — Rumah123')

@push('styles')
    <x-seo-meta
        :title="$title.' — Rumah123'"
        :description="$intro"
        :canonical="route('info.show', $page)"
    />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground flex items-center gap-1.5" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
        <span aria-hidden="true">/</span>
        <span class="text-foreground font-semibold">{{ $title }}</span>
    </nav>

    <h1 class="mt-3 text-2xl font-extrabold tracking-tight">{{ $title }}</h1>
    <p class="mt-1 text-sm text-muted-foreground max-w-2xl">{{ $intro }}</p>

    <div class="mt-6 grid gap-4 max-w-3xl">
        @foreach ($sections as $section)
            <section class="kt-card p-5">
                <h2 class="font-bold">{{ $section['heading'] }}</h2>
                <p class="mt-1.5 text-sm text-muted-foreground leading-relaxed">{{ $section['body'] }}</p>
            </section>
        @endforeach
    </div>
</div>
@endsection
