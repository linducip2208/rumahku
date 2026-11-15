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
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Daftar Properti</h1>
    <p class="text-sm text-muted-foreground mt-1">Filter dan temukan properti sesuai kebutuhanmu.</p>

    <form method="GET" action="{{ route('properties.index') }}" class="mt-4 kt-card p-4 grid gap-2 md:grid-cols-[1fr_150px_150px_150px_130px_130px_110px_auto]">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Kata kunci…" class="kt-input" />
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
        <button type="submit" class="kt-btn kt-btn-primary md:col-span-2"><i class="ki-filled ki-magnifier"></i> Filter</button>
    </form>

    <div class="mt-6">
        @if ($properties->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Tidak ada properti yang cocok dengan filter. Coba ubah kata kunci atau filter.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
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
                            <span class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ $property->listing_status === 'jual' ? 'Dijual' : 'Disewa' }} &middot; {{ ucfirst($property->type) }}</span>
                            <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                            <div class="font-semibold mt-1 line-clamp-1">{{ $property->title }}</div>
                            <div class="text-xs text-muted-foreground mt-1">
                                {{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; LB {{ $property->building_area ?? '-' }}m² &middot; {{ $property->city }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $properties->links() }}</div>
        @endif
    </div>
</div>
@endsection
