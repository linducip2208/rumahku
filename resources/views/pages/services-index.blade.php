@extends('layouts.metronic')

@section('title', 'Direktori Jasa — Rumah123')

@push('styles')
    <x-seo-meta
        title="Direktori Jasa — Rumah123"
        description="Direktori jasa properti: notaris, inspeksi, KPR, dan renovasi terpercaya di kotamu."
        :canonical="route('services.index')"
    />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Direktori Jasa</h1>
    <p class="text-sm text-muted-foreground mt-1">Notaris, inspeksi, KPR, dan renovasi di kotamu.</p>

    <form method="GET" action="{{ route('services.index') }}" class="mt-4 kt-card p-4 grid gap-2 md:grid-cols-[200px_1fr_auto]">
        <select name="category" class="kt-select">
            <option value="">Semua Kategori</option>
            @foreach (['notaris' => 'Notaris', 'inspeksi' => 'Inspeksi', 'kpr' => 'KPR', 'renovasi' => 'Renovasi'] as $val => $label)
                <option value="{{ $val }}" @selected(($filters['category'] ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Kota" class="kt-input" />
        <button type="submit" class="kt-btn kt-btn-primary">Filter</button>
    </form>

    @if ($services->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada jasa terdaftar.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-6">
            @foreach ($services as $service)
                <div class="kt-card p-5">
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-primary">{{ ucfirst($service->category) }}</span>
                    <div class="font-bold mt-1">{{ $service->name }}</div>
                    <div class="text-xs text-muted-foreground">{{ $service->city }} &middot; {{ $service->phone }}</div>
                    @if ($service->description)
                        <p class="text-sm text-muted-foreground mt-2 line-clamp-3">{{ $service->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $services->links() }}</div>
    @endif
</div>
@endsection
