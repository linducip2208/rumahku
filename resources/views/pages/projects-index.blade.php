@extends('layouts.metronic')

@section('title', 'Proyek Properti Baru — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Proyek Properti Baru</h1>
    <p class="text-sm text-muted-foreground mt-1">Temukan proyek terbaru dari developer terpercaya.</p>
    <div class="mt-6">
        @if ($projects->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Belum ada proyek. Data akan tampil setelah proyek ditambahkan.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project->slug) }}" class="kt-card p-5 hover:shadow-md transition">
                        <div class="font-bold text-lg line-clamp-1">{{ $project->name }}</div>
                        <div class="text-xs text-muted-foreground mt-1">{{ $project->city }}</div>
                        <div class="text-primary font-extrabold mt-2">Mulai Rp {{ number_format($project->price_from, 0, ',', '.') }}</div>
                        <div class="text-sm text-muted-foreground mt-2 line-clamp-2">{{ $project->description }}</div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $projects->links() }}</div>
        @endif
    </div>
</div>
@endsection
