@extends('layouts.metronic')

@section('title', ($project->name ?? 'Detail Proyek') . ' — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground mb-2">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('projects.index') }}" class="hover:underline">Proyek</a> /
        <span>{{ $project->name }}</span>
    </nav>
    <h1 class="text-2xl lg:text-3xl font-extrabold">{{ $project->name }}</h1>
    <p class="text-sm text-muted-foreground mt-1">{{ $project->city }} &middot; Developer: {{ optional($project->developer)->name ?? '-' }}</p>
    <div class="text-xl font-extrabold text-primary mt-3">Mulai Rp {{ number_format($project->price_from, 0, ',', '.') }}</div>
    <div class="kt-card p-5 mt-4">
        <h2 class="font-bold mb-2">Tentang Proyek</h2>
        <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $project->description }}</div>
    </div>

    @if ($properties->isNotEmpty())
        <h2 class="font-bold mt-8 mb-3">Unit & Listing Developer Terkait</h2>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($properties as $property)
                @php $cover = $property->images->first()?->path; @endphp
                <a href="{{ route('properties.show', $property->slug) }}" class="kt-card overflow-hidden hover:shadow-md transition">
                    <div class="aspect-[4/3] bg-muted grid place-items-center text-muted-foreground text-sm">
                        @if ($cover)
                            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="size-full object-cover" loading="lazy" />
                        @else
                            <i class="ki-filled ki-home text-3xl"></i>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                        <div class="font-semibold mt-1 line-clamp-1">{{ $property->title }}</div>
                        <div class="text-xs text-muted-foreground mt-1">{{ $property->city }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
