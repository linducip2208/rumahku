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
    $view = request('view', 'grid');
    $isList = $view === 'list';
    $isMap = $view === 'map';
    $activeFilters = array_filter($filters, fn ($v) => $v !== null && $v !== '');
    $chipUrl = function (array $without) {
        $q = request()->except(array_merge($without, ['page']));
        $qs = http_build_query($q);
        return request()->url() . ($qs ? '?' . $qs : '');
    };
    $viewUrl = function (?string $v) {
        $q = request()->except(['page', 'view']);
        if ($v && $v !== 'grid') {
            $q['view'] = $v;
        }
        $qs = http_build_query($q);
        return request()->url() . ($qs ? '?' . $qs : '');
    };
    $filterLabels = [
        'q' => 'Kata kunci',
        'type' => 'Tipe',
        'status' => 'Status',
        'city' => 'Lokasi',
        'min_price' => 'Harga min',
        'max_price' => 'Harga maks',
        'bedrooms' => 'KT min',
    ];
    $pricePresets = [
        '' => 'Harga',
        '0-500000000' => '< Rp 500 jt',
        '500000000-1000000000' => 'Rp 500 jt – 1 M',
        '1000000000-2000000000' => 'Rp 1 – 2 M',
        '2000000000-5000000000' => 'Rp 2 – 5 M',
        '5000000000-' => '> Rp 5 M',
    ];
    $currentPreset = '';
    if (! empty($filters['min_price']) || ! empty($filters['max_price'])) {
        $currentPreset = ($filters['min_price'] ?? '') . '-' . ($filters['max_price'] ?? '');
    }
    $mapPoints = collect($properties->items())
        ->filter(fn ($p) => ! is_null($p->lat) && ! is_null($p->lng))
        ->map(fn ($p) => [
            'lat' => (float) $p->lat,
            'lng' => (float) $p->lng,
            'title' => $p->title,
            'price' => 'Rp ' . number_format($p->price, 0, ',', '.'),
            'url' => route('properties.show', $p->slug),
        ])
        ->values();
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-8">
    <nav class="text-xs text-muted-foreground mb-3" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a>
        <span class="mx-1">/</span>
        <a href="{{ route('properties.index') }}" class="hover:underline">Properti</a>
        @if (! empty($filters['city']))
            <span class="mx-1">/</span>
            <span class="text-foreground font-medium">{{ $filters['city'] }}</span>
        @endif
    </nav>

    {{-- Toolbar ala mockup: pill responsif + tombol Cari --}}
    <form method="GET" action="{{ route('properties.index') }}" id="toolbar-filter" class="flex flex-col gap-2 xl:flex-row xl:items-center">
        @if ($isList || $isMap)
            <input type="hidden" name="view" value="{{ $view }}" />
        @endif
        @if (! empty($filters['q']))
            <input type="hidden" name="q" value="{{ $filters['q'] }}" />
        @endif
        <input type="hidden" name="min_price" id="toolbar-min-price" value="{{ $filters['min_price'] ?? '' }}" />
        <input type="hidden" name="max_price" id="toolbar-max-price" value="{{ $filters['max_price'] ?? '' }}" />
        <label class="flex flex-1 items-center gap-2 rounded-full border border-border bg-white px-4 py-2.5 text-sm shadow-sm focus-within:border-blue-500">
            <i class="ki-filled ki-geolocation text-base text-muted-foreground"></i>
            <span class="sr-only">Lokasi</span>
            <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Location" class="w-full bg-transparent outline-none placeholder:text-muted-foreground" />
        </label>
        <label class="flex flex-1 items-center gap-2 rounded-full border border-border bg-white px-4 py-2.5 text-sm shadow-sm focus-within:border-blue-500">
            <i class="ki-filled ki-home text-base text-muted-foreground"></i>
            <span class="sr-only">Tipe properti</span>
            <select name="type" class="w-full bg-transparent outline-none">
                <option value="">Property type</option>
                @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                    <option value="{{ $val }}" @selected(($filters['type'] ?? '') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-1 items-center gap-2 rounded-full border border-border bg-white px-4 py-2.5 text-sm shadow-sm focus-within:border-blue-500">
            <i class="ki-filled ki-wallet text-base text-muted-foreground"></i>
            <span class="sr-only">Rentang harga</span>
            <select id="toolbar-price-preset" class="w-full bg-transparent outline-none">
                @foreach ($pricePresets as $val => $label)
                    <option value="{{ $val }}" @selected($currentPreset === (string) $val)>{{ $label === 'Harga' ? 'Price range' : $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-1 items-center gap-2 rounded-full border border-border bg-white px-4 py-2.5 text-sm shadow-sm focus-within:border-blue-500">
            <i class="ki-filled ki-bed text-base text-muted-foreground"></i>
            <span class="sr-only">Kamar tidur</span>
            <select name="bedrooms" class="w-full bg-transparent outline-none">
                <option value="">Beds &amp; Rooms</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" @selected(($filters['bedrooms'] ?? '') == (string) $i)>{{ $i }}+ KT</option>
                @endfor
            </select>
        </label>
        <button type="submit" class="rounded-full bg-blue-600 px-8 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 xl:min-w-36">
            Search
        </button>
    </form>

    <div class="mt-4 flex flex-wrap items-end justify-between gap-3">
        <p class="text-sm text-muted-foreground">Result <span class="font-bold text-foreground">{{ $properties->total() }}</span> property</p>
        <div class="flex items-center gap-1 rounded-full border border-border bg-white p-1 text-sm shadow-sm" role="group" aria-label="Tampilan hasil">
            <a href="{{ $viewUrl(null) }}" class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 {{ ! $isList && ! $isMap ? 'bg-blue-50 font-semibold text-blue-700 ring-1 ring-blue-600' : 'text-muted-foreground hover:bg-muted' }}">
                <i class="ki-filled ki-element-3 text-base"></i> Grid
            </a>
            <a href="{{ $viewUrl('list') }}" class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 {{ $isList ? 'bg-blue-50 font-semibold text-blue-700 ring-1 ring-blue-600' : 'text-muted-foreground hover:bg-muted' }}">
                <i class="ki-filled ki-row-horizontal text-base"></i> List
            </a>
            <a href="{{ $viewUrl('map') }}" class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 {{ $isMap ? 'bg-blue-50 font-semibold text-blue-700 ring-1 ring-blue-600' : 'text-muted-foreground hover:bg-muted' }}">
                <i class="ki-filled ki-map text-base"></i> Map
            </a>
        </div>
    </div>

    @if (! empty($activeFilters))
        <div class="flex flex-wrap items-center gap-2 mt-3">
            @foreach ($activeFilters as $key => $value)
                @if ($key === 'view') @continue @endif
                <span class="inline-flex items-center gap-1.5 text-xs rounded-full bg-blue-600/10 text-blue-700 px-2.5 py-1 font-medium">
                    {{ $filterLabels[$key] ?? $key }}: {{ is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value }}
                    <a href="{{ $chipUrl([$key]) }}" class="hover:text-destructive font-bold" aria-label="Hapus filter {{ $filterLabels[$key] ?? $key }}">&times;</a>
                </span>
            @endforeach
            <a href="{{ $viewUrl($isList ? 'list' : ($isMap ? 'map' : null)) }}" class="text-xs text-muted-foreground hover:text-destructive hover:underline">Reset semua</a>
        </div>
    @endif

    <div class="mt-4 flex flex-col lg:flex-row gap-5 items-start">
        <details class="lg:hidden w-full rounded-2xl border border-border bg-white shadow-sm">
            <summary class="cursor-pointer p-4 font-bold text-sm list-none flex items-center justify-between">
                <span><i class="ki-filled ki-setting-2 mr-1"></i> Filter Properti{{ count($activeFilters) ? ' (' . count($activeFilters) . ')' : '' }}</span>
                <span class="text-muted-foreground text-xs font-normal">Ketuk untuk buka/tutup</span>
            </summary>
            <div class="px-4 pb-4">
                <form method="GET" action="{{ route('properties.index') }}" class="grid gap-2">
                    @if ($isList || $isMap)
                        <input type="hidden" name="view" value="{{ $view }}" />
                    @endif
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Kata kunci…" class="kt-input" />
                    <div class="grid grid-cols-2 gap-2 rounded-xl border border-border p-1 text-sm font-semibold" role="group" aria-label="Status">
                        @php $st = $filters['status'] ?? ''; @endphp
                        <label class="cursor-pointer rounded-lg px-3 py-2 text-center {{ $st === '' ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="" class="sr-only" @checked($st === '') /> Semua
                        </label>
                        <label class="cursor-pointer rounded-lg px-3 py-2 text-center {{ in_array($st, ['jual', 'dijual'], true) ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="jual" class="sr-only" @checked(in_array($st, ['jual', 'dijual'], true)) /> Dijual
                        </label>
                        <label class="cursor-pointer rounded-lg px-3 py-2 text-center {{ in_array($st, ['sewa', 'disewa'], true) ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="sewa" class="sr-only" @checked(in_array($st, ['sewa', 'disewa'], true)) /> Disewa
                        </label>
                        <span class="hidden"></span>
                    </div>
                    <select name="type" class="kt-select">
                        <option value="">Semua Tipe</option>
                        @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['type'] ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Kota" class="kt-input" />
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Harga min" min="0" class="kt-input" />
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Harga maks" min="0" class="kt-input" />
                    </div>
                    <input type="number" name="bedrooms" value="{{ $filters['bedrooms'] ?? '' }}" placeholder="KT min" min="0" class="kt-input" />
                    <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-magnifier"></i> Filter</button>
                </form>
            </div>
        </details>

        <aside class="hidden lg:block lg:w-72 shrink-0 lg:sticky lg:top-20">
            <div class="rounded-2xl border border-border bg-white p-4 shadow-sm">
                <h2 class="font-bold text-sm mb-3">Filter Properti</h2>
                <form method="GET" action="{{ route('properties.index') }}" class="grid gap-2">
                    @if ($isList || $isMap)
                        <input type="hidden" name="view" value="{{ $view }}" />
                    @endif
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Kata kunci…" class="kt-input" />
                    <div class="grid grid-cols-3 gap-1 rounded-xl border border-border p-1 text-[13px] font-semibold" role="group" aria-label="Status">
                        @php $st = $filters['status'] ?? ''; @endphp
                        <label class="cursor-pointer rounded-lg px-2 py-2 text-center {{ $st === '' ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="" class="sr-only" @checked($st === '') /> Semua
                        </label>
                        <label class="cursor-pointer rounded-lg px-2 py-2 text-center {{ in_array($st, ['jual', 'dijual'], true) ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="jual" class="sr-only" @checked(in_array($st, ['jual', 'dijual'], true)) /> Jual
                        </label>
                        <label class="cursor-pointer rounded-lg px-2 py-2 text-center {{ in_array($st, ['sewa', 'disewa'], true) ? 'bg-blue-600 text-white' : 'hover:bg-muted' }}">
                            <input type="radio" name="status" value="sewa" class="sr-only" @checked(in_array($st, ['sewa', 'disewa'], true)) /> Sewa
                        </label>
                    </div>
                    <select name="type" class="kt-select">
                        <option value="">Semua Tipe</option>
                        @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['type'] ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="Kota" class="kt-input" />
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Harga min" min="0" class="kt-input" />
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Harga maks" min="0" class="kt-input" />
                    </div>
                    <input type="number" name="bedrooms" value="{{ $filters['bedrooms'] ?? '' }}" placeholder="KT min" min="0" class="kt-input" />
                    <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-magnifier"></i> Filter</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0 w-full">
            @if ($properties->total() === 0)
                <div class="rounded-2xl border border-dashed border-border bg-white p-10 text-center text-muted-foreground">
                    Tidak ada properti yang cocok dengan filter. Coba ubah kata kunci atau filter.
                </div>
            @else
                @if ($isMap)
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <div id="property-map" class="z-0 h-[420px] w-full overflow-hidden rounded-2xl border border-border shadow-sm"></div>
                    <p class="mt-2 text-xs text-muted-foreground">{{ $mapPoints->count() }} dari {{ $properties->total() }} properti memiliki koordinat dan tampil di peta (halaman ini).</p>
                    @if ($mapPoints->isEmpty())
                        <div class="mt-3 rounded-2xl border border-dashed border-border bg-white p-10 text-center text-muted-foreground">
                            Tidak ada properti di halaman ini yang memiliki koordinat peta.
                        </div>
                    @endif
                    <div class="mt-4 grid gap-3 sm:gap-5 grid-cols-2 xl:grid-cols-3">
                        @foreach ($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                @elseif ($isList)
                    <div class="grid gap-3">
                        @foreach ($properties as $property)
                            @php $cover = $property->images->first()?->path ?? ($property->cover ?? null); @endphp
                            <a href="{{ route('properties.show', $property->slug) }}" class="rounded-2xl border border-border bg-white p-3 flex gap-3 shadow-sm hover:shadow-md transition">
                                <div class="w-28 sm:w-40 shrink-0 aspect-[4/3] rounded-xl overflow-hidden bg-muted grid place-items-center text-muted-foreground text-xs">
                                    @if ($cover)
                                        <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="size-full object-cover" loading="lazy" />
                                    @else
                                        <i class="ki-filled ki-home text-2xl"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ ucfirst($property->type ?? '') }} &middot; {{ $property->city }}</div>
                                    <div class="text-blue-700 font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}{{ ($property->listing_status ?? 'jual') === 'sewa' ? '/thn' : '' }}</div>
                                    <div class="font-semibold text-sm mt-0.5 line-clamp-1">{{ $property->title }}</div>
                                    <div class="text-xs text-muted-foreground mt-1 line-clamp-1">{{ $property->address }} — {{ $property->district }}</div>
                                    <div class="text-xs text-muted-foreground mt-1">{{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; LB {{ $property->building_area ?? '-' }}m²</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="grid gap-3 sm:gap-5 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($properties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                @endif
                <div class="mt-6 flex justify-center">{{ $properties->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('toolbar-filter');
    var preset = document.getElementById('toolbar-price-preset');
    var minInput = document.getElementById('toolbar-min-price');
    var maxInput = document.getElementById('toolbar-max-price');
    if (form && preset && minInput && maxInput) {
        form.addEventListener('submit', function () {
            var val = preset.value || '';
            if (val === '') {
                minInput.value = '';
                maxInput.value = '';
            } else {
                var parts = val.split('-');
                minInput.value = parts[0] || '';
                maxInput.value = parts[1] || '';
            }
        });
    }
});
</script>
@if ($isMap)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var points = @json($mapPoints);
    var el = document.getElementById('property-map');
    if (!el || typeof L === 'undefined') {
        return;
    }
    var center = points.length ? [points[0].lat, points[0].lng] : [-6.2, 106.85];
    var map = L.map('property-map').setView(center, points.length ? 11 : 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    points.forEach(function (p) {
        L.marker([p.lat, p.lng]).addTo(map)
            .bindPopup('<a href="' + p.url + '"><strong>' + p.title + '</strong></a><br>' + p.price);
    });
});
</script>
@endif
@endpush
