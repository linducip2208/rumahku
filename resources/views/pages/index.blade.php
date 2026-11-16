@extends('layouts.metronic')

@section('title', 'Daftar Properti — Rumah123')

@push('styles')
    <x-seo-meta
        title="Daftar Properti — Rumah123"
        description="Jelajahi daftar properti dijual & disewa di Indonesia: rumah, apartemen, ruko, tanah, dan villa pilihan."
        :canonical="route('properties.index')"
    />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-8">
    <h1 class="text-2xl font-extrabold tracking-tight">Daftar Properti</h1>
    <p class="text-sm text-muted-foreground mt-1">Filter dan temukan properti sesuai kebutuhanmu.</p>

    <form method="GET" action="{{ route('properties.index') }}" class="mt-4 kt-card p-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-[1fr_150px_150px_150px_130px_130px_90px_auto] lg:sticky lg:top-20 z-20">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Kata kunci…" class="kt-input sm:col-span-2 lg:col-span-1" />
        <select name="type" class="kt-select">
            <option value="">Semua Tipe</option>
            @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                <option value="{{ $val }}" @selected(($filters['type'] ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="kt-select">
            <option value="">Jual / Sewa</option>
            <option value="jual" @selected(in_array($filters['status'] ?? '', ['jual', 'dijual'], true))>Dijual</option>
            <option value="sewa" @selected(in_array($filters['status'] ?? '', ['sewa', 'disewa'], true))>Disewa</option>
        </select>
        <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Kota" class="kt-input" />
        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Harga min" class="kt-input" />
        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Harga maks" class="kt-input" />
        <input type="number" name="bedrooms" value="{{ $filters['bedrooms'] ?? '' }}" placeholder="KT min" class="kt-input" min="0" />
        <button type="submit" class="kt-btn kt-btn-primary sm:col-span-2 lg:col-span-1"><i class="ki-filled ki-magnifier"></i> Filter</button>
    </form>

    <div class="mt-6">
        @if ($properties->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Tidak ada properti yang cocok dengan filter. Coba ubah kata kunci atau filter.
            </div>
        @else
            <p class="text-xs text-muted-foreground mb-3">{{ $properties->total() }} properti ditemukan</p>
            <div class="grid gap-3 sm:gap-5 grid-cols-2 lg:grid-cols-3">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
            <div class="mt-6 flex justify-center">{{ $properties->links() }}</div>
        @endif
    </div>
</div>
@endsection
