@extends('layouts.metronic')

@section('title', 'Rumah123 — Jual, Sewa & Proyek Baru')

@push('styles')
    <x-seo-meta
        title="Rumah123 — Jual, Sewa & Proyek Baru"
        description="Rumah123 — Portal properti Indonesia: cari rumah, apartemen, tanah, ruko, villa dijual & disewa, proyek baru, agen, dan simulasi KPR."
        :canonical="route('home')"
    />
@endpush

@section('content')
<!-- Hero -->
<section class="bg-gradient-to-b from-muted to-background border-b border-border">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-12 lg:py-16">
        <h1 class="text-3xl lg:text-5xl font-extrabold tracking-tight">Temukan Rumah Impianmu</h1>
        <p class="text-muted-foreground mt-2 max-w-xl">Jelajahi ribuan properti dijual & disewa di seluruh Indonesia.</p>
        <form method="GET" action="{{ route('properties.index') }}" class="mt-6 bg-card border border-border rounded-2xl p-3 grid gap-2 md:grid-cols-[1fr_180px_180px_180px_auto] shadow-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci, lokasi, atau nama proyek…" class="kt-input" />
            <select name="type" class="kt-select">
                <option value="">Semua Tipe</option>
                @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="kt-select">
                <option value="">Jual / Sewa</option>
                <option value="jual" @selected(request('status') === 'jual')>Dijual</option>
                <option value="sewa" @selected(request('status') === 'sewa')>Disewa</option>
            </select>
            <input type="text" name="city" value="{{ request('city') }}" placeholder="Kota" class="kt-input" />
            <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-magnifier"></i> Cari</button>
        </form>
    </div>
</section>

<!-- Featured -->
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Properti Unggulan</h2>
        <a href="{{ route('properties.index') }}" class="text-sm text-primary hover:underline">Lihat Semua</a>
    </div>
    @if ($featured->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
            Belum ada properti unggulan. Data akan tampil setelah properti dipublikasikan.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featured as $property)
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
                        <div class="text-xs text-muted-foreground mt-1 line-clamp-1">
                            {{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; {{ $property->city }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

<!-- Cities -->
<section class="border-y border-border bg-muted/50">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
        <h2 class="text-xl font-bold mb-4">Kota Populer</h2>
        @php
            $fallbackCities = collect([
                ['city' => 'Jakarta Selatan', 'total' => null],
                ['city' => 'Surabaya', 'total' => null],
                ['city' => 'Bandung', 'total' => null],
                ['city' => 'Bekasi', 'total' => null],
                ['city' => 'Depok', 'total' => null],
                ['city' => 'Jakarta Timur', 'total' => null],
            ]);
            $cityList = $cities->isNotEmpty() ? $cities : $fallbackCities;
        @endphp
        <div class="grid gap-3 grid-cols-2 md:grid-cols-4">
            @foreach ($cityList as $row)
                @php $cityName = is_array($row) ? $row['city'] : $row->city; @endphp
                <a href="{{ route('properties.index', ['city' => $cityName]) }}" class="kt-card p-4 flex items-center justify-between hover:shadow-md transition">
                    <span class="font-semibold">{{ $cityName }}</span>
                    <i class="ki-filled ki-right text-muted-foreground"></i>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Agent -->
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="kt-card p-8 lg:p-10 grid gap-6 lg:grid-cols-2 items-center bg-primary text-primary-foreground border-0">
        <div>
            <h2 class="text-2xl font-extrabold">Anda Agen Properti?</h2>
            <p class="opacity-80 mt-1">Pasarkan listing Anda ke jutaan pencari properti dan kelola leads dalam satu dasbor.</p>
        </div>
        <form method="POST" action="{{ route('leads.store') }}" class="grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
            @csrf
            <input type="text" name="name" required placeholder="Nama" class="kt-input bg-white text-black" />
            <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input bg-white text-black" />
            <button type="submit" class="kt-btn bg-white text-black hover:bg-white/90 font-semibold">Daftar Jadi Agen</button>
        </form>
    </div>
</section>
@endsection
