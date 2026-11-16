@extends('layouts.metronic')

@section('title', 'Daftar Properti — Rumah123')

@push('styles')
    <x-seo-meta
        title="Daftar Properti — Rumah123"
        description="Jelajahi daftar properti dijual & disewa di Indonesia: rumah, apartemen, ruko, tanah, dan villa pilihan."
        :canonical="route('properties.index')"
    />
@endpush

@php
    $filters = $filters ?? [];
    $activeFilters = array_filter($filters, fn ($v) => $v !== null && $v !== '');
    $chipUrl = function (array $without) {
        $q = request()->except(array_merge($without, ['page']));
        $qs = http_build_query($q);
        return request()->url() . ($qs ? '?' . $qs : '');
    };
    $baseQuery = request()->except('page');
    $gridQuery = $baseQuery;
    unset($gridQuery['view']);
    $gridUrl = request()->url() . ($gridQuery ? '?' . http_build_query($gridQuery) : '');
    $listUrl = request()->url() . '?' . http_build_query(array_merge($baseQuery, ['view' => 'list']));
    $isList = request('view') === 'list';
    $filterLabels = [
        'q' => 'Kata kunci',
        'type' => 'Tipe',
        'status' => 'Status',
        'city' => 'Kota',
        'min_price' => 'Harga min',
        'max_price' => 'Harga maks',
        'bedrooms' => 'KT min',
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-8">
    <nav class="text-xs text-muted-foreground mb-2" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:underline">Home</a>
        <span class="mx-1">/</span>
        <a href="{{ route('properties.index') }}" class="hover:underline">Properti</a>
        @if (! empty($filters['city']))
            <span class="mx-1">/</span>
            <span class="text-foreground font-medium">{{ $filters['city'] }}</span>
        @endif
    </nav>

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">Daftar Properti</h1>
            <p class="text-sm text-muted-foreground mt-1">{{ $properties->total() }} properti{{ ! empty($filters['city']) ? ' di ' . $filters['city'] : '' }} ditemukan</p>
        </div>
        <div class="flex items-center gap-1 rounded-xl border border-border p-1 text-sm" role="group" aria-label="Tampilan hasil">
            <a href="{{ $gridUrl }}" class="px-3 py-1.5 rounded-lg {{ ! $isList ? 'bg-primary text-white font-semibold' : 'text-muted-foreground hover:bg-muted' }}">Grid</a>
            <a href="{{ $listUrl }}" class="px-3 py-1.5 rounded-lg {{ $isList ? 'bg-primary text-white font-semibold' : 'text-muted-foreground hover:bg-muted' }}">List</a>
        </div>
    </div>

    @if (! empty($activeFilters))
        <div class="flex flex-wrap items-center gap-2 mt-3">
            @foreach ($activeFilters as $key => $value)
                <span class="inline-flex items-center gap-1.5 text-xs rounded-full bg-primary/10 text-primary px-2.5 py-1 font-medium">
                    {{ $filterLabels[$key] ?? $key }}: {{ is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value }}
                    <a href="{{ $chipUrl([$key]) }}" class="hover:text-destructive font-bold" aria-label="Hapus filter {{ $filterLabels[$key] ?? $key }}">&times;</a>
                </span>
            @endforeach
            <a href="{{ route('properties.index', $isList ? ['view' => 'list'] : []) }}" class="text-xs text-muted-foreground hover:text-destructive hover:underline">Hapus semua</a>
        </div>
    @endif

    <div class="mt-4 flex flex-col lg:flex-row gap-5 items-start">
        <details class="lg:hidden w-full kt-card">
            <summary class="cursor-pointer p-4 font-bold text-sm list-none flex items-center justify-between">
                <span><i class="ki-filled ki-setting-2 mr-1"></i> Filter Properti{{ count($activeFilters) ? ' (' . count($activeFilters) . ')' : '' }}</span>
                <span class="text-muted-foreground text-xs">Ketuk untuk buka/tutup</span>
            </summary>
            <div class="px-4 pb-4">
                <form method="GET" action="{{ route('properties.index') }}" class="grid gap-2">
                    @if ($isList)
                        <input type="hidden" name="view" value="list" />
                    @endif
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
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Harga min" class="kt-input" />
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Harga maks" class="kt-input" />
                    </div>
                    <input type="number" name="bedrooms" value="{{ $filters['bedrooms'] ?? '' }}" placeholder="KT min" class="kt-input" min="0" />
                    <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-magnifier"></i> Filter</button>
                </form>
            </div>
        </details>

        <aside class="hidden lg:block lg:w-72 shrink-0 lg:sticky lg:top-20">
            <div class="kt-card p-4">
                <h2 class="font-bold text-sm mb-3">Filter Properti</h2>
                <form method="GET" action="{{ route('properties.index') }}" class="grid gap-2">
                    @if ($isList)
                        <input type="hidden" name="view" value="list" />
                    @endif
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
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Harga min" class="kt-input" />
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Harga maks" class="kt-input" />
                    </div>
                    <input type="number" name="bedrooms" value="{{ $filters['bedrooms'] ?? '' }}" placeholder="KT min" class="kt-input" min="0" />
                    <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-magnifier"></i> Filter</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0 w-full">
            @if ($properties->total() === 0)
                <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                    Tidak ada properti yang cocok dengan filter. Coba ubah kata kunci atau filter.
                </div>
            @else
                @if ($isList)
                    <div class="grid gap-3">
                        @foreach ($properties as $property)
                            @php $cover = $property->images->first()?->path ?? ($property->cover ?? null); @endphp
                            <a href="{{ route('properties.show', $property->slug) }}" class="kt-card p-3 flex gap-3 hover:shadow-md transition">
                                <div class="w-28 sm:w-40 shrink-0 aspect-[4/3] rounded-lg overflow-hidden bg-muted grid place-items-center text-muted-foreground text-xs">
                                    @if ($cover)
                                        <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="size-full object-cover" loading="lazy" />
                                    @else
                                        <i class="ki-filled ki-home text-2xl"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ ucfirst($property->type ?? '') }} &middot; {{ $property->city }}</div>
                                    <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                                    <div class="font-semibold text-sm mt-0.5 line-clamp-1">{{ $property->title }}</div>
                                    <div class="text-xs text-muted-foreground mt-1 line-clamp-1">{{ $property->address }} — {{ $property->district }}</div>
                                    <div class="text-xs text-muted-foreground mt-1">{{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; LB {{ $property->building_area ?? '-' }}m²</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="grid gap-3 sm:gap-5 grid-cols-2 lg:grid-cols-3">
                        @foreach ($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                @endif
                <div class="mt-6 flex justify-center">{{ $properties->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
