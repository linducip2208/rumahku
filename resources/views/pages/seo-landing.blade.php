@extends('layouts.metronic')

@section('title', $metaTitle ?? ($heading . ' — Rumah123'))

@push('styles')
@if (! empty($canonical ?? null))
<link rel="canonical" href="{{ $canonical }}" />
<meta name="description" content="{{ $metaDesc ?? '' }}" />
@endif
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground mb-2">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('properties.index') }}" class="hover:underline">Properti</a> /
        <span>{{ $heading }}</span>
    </nav>
    <h1 class="text-2xl lg:text-3xl font-extrabold">{{ $heading }}</h1>
    <p class="text-sm text-muted-foreground mt-2 max-w-3xl">{{ $metaDesc }}</p>

    <div class="mt-6">
        @if ($properties->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Belum ada listing {{ strtolower($heading) }}. Jelajahi properti lain atau hubungi agen kami.
            </div>
        @else
            <p class="text-sm text-muted-foreground mb-4">{{ $properties->total() }} properti ditemukan.</p>
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
                            <span class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ $listing === 'jual' ? 'Dijual' : 'Disewa' }} &middot; {{ ucfirst($type) }}</span>
                            <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                            <div class="font-semibold mt-1 line-clamp-1">{{ $property->title }}</div>
                            <div class="text-xs text-muted-foreground mt-1">
                                {{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; {{ $property->city }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $properties->links() }}</div>
        @endif
    </div>

    <div class="kt-card p-5 mt-8">
        <h2 class="font-bold mb-2">Tentang {{ $heading }}</h2>
        <p class="text-sm text-muted-foreground">
            {{ $heading }} menawarkan berbagai pilihan {{ $type }} {{ $listing === 'jual' ? 'dijual' : 'disewa' }}
            di {{ $cityName }}. Bandingkan harga, lokasi, dan fasilitas sebelum menghubungi agen terverifikasi Rumah123
            untuk survei dan negosiasi.
        </p>
    </div>

    <div class="kt-card p-5 mt-4">
        <h2 class="font-bold mb-3">Pertanyaan Umum (FAQ)</h2>
        <div class="grid gap-3 text-sm">
            <div>
                <div class="font-semibold">Berapa harga {{ strtolower($type) }} {{ $listing }} di {{ $cityName }}?</div>
                <p class="text-muted-foreground">Harga bervariasi tergantung lokasi, luas, dan fasilitas. Gunakan filter harga di halaman properti untuk kisaran terkini.</p>
            </div>
            <div>
                <div class="font-semibold">Area mana yang paling dicari di {{ $cityName }}?</div>
                <p class="text-muted-foreground">Area dekat akses tol, stasiun, sekolah, dan pusat bisnis biasanya paling diminati pembeli dan penyewa.</p>
            </div>
            <div>
                <div class="font-semibold">Apa yang perlu dicek sebelum membeli {{ strtolower($type) }}?</div>
                <p class="text-muted-foreground">Cek sertifikat, IMB/PBG, riwayat banjir, akses jalan, dan legalitas penjual sebelum tanda jadi.</p>
            </div>
            <div>
                <div class="font-semibold">Bisakah KPR untuk properti ini?</div>
                <p class="text-muted-foreground">Ya, sebagian besar bank melayani KPR untuk {{ strtolower($type) }} bersertifikat. Gunakan kalkulator KPR di halaman detail properti untuk estimasi cicilan.</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="font-bold mb-3">Jelajahi Area & Tipe Lain</h2>
        <div class="flex flex-wrap gap-2 text-sm">
            @php
                $otherCities = ['jakarta-selatan', 'jakarta-timur', 'bandung', 'surabaya', 'bekasi', 'depok'];
                $otherTypes = ['rumah', 'apartemen', 'tanah', 'ruko', 'villa'];
            @endphp
            @foreach ($otherCities as $c)
                @if ($c !== ($citySlug ?? ''))
                    <a href="{{ route('seo.landing', ['listing' => $listing, 'type' => $type, 'city' => $c]) }}" class="px-3 py-1.5 rounded-full border border-border hover:bg-muted">{{ ucfirst($listing) }} {{ ucfirst($type) }} di {{ ucwords(str_replace('-', ' ', $c)) }}</a>
                @endif
            @endforeach
            @foreach ($otherTypes as $t)
                @if ($t !== $type)
                    <a href="{{ route('seo.landing', ['listing' => $listing, 'type' => $t, 'city' => $citySlug]) }}" class="px-3 py-1.5 rounded-full border border-border hover:bg-muted">{{ ucfirst($listing) }} {{ ucfirst($t) }} di {{ $cityName }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
