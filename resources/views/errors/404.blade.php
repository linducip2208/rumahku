@extends('layouts.metronic')

@section('title', 'Halaman Tidak Ditemukan (404) — Rumah123')

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-16 text-center">
    <div class="mx-auto mb-6 grid place-items-center size-20 rounded-2xl bg-blue-50 border border-blue-100 text-4xl" aria-hidden="true">
        🏠
    </div>
    <p class="text-sm font-semibold text-blue-600 mb-2">Error 404</p>
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Halaman tidak ditemukan</h1>
    <p class="text-muted-foreground mb-8">Maaf, alamat yang kamu buka sudah pindah atau tidak tersedia. Coba kembali ke beranda atau jelajahi kota populer di bawah.</p>
    <div class="flex flex-wrap items-center justify-center gap-3 mb-10">
        <a href="{{ route('home') }}" class="kt-btn kt-btn-primary">Kembali ke Beranda</a>
        <button type="button" onclick="history.back()" class="kt-btn kt-btn-outline">Kembali ke Sebelumnya</button>
    </div>
    <div class="rounded-2xl border border-border bg-muted/40 p-6 text-left">
        <p class="font-semibold mb-3 text-center">Jelajahi kota populer</p>
        <div class="flex flex-wrap justify-center gap-2 text-sm">
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('seo.landing', ['listing' => 'jual', 'type' => 'rumah', 'city' => 'depok']) }}">Rumah dijual di Depok</a>
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('seo.landing', ['listing' => 'jual', 'type' => 'rumah', 'city' => 'jakarta-selatan']) }}">Rumah dijual di Jakarta Selatan</a>
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('seo.landing', ['listing' => 'jual', 'type' => 'rumah', 'city' => 'bekasi']) }}">Rumah dijual di Bekasi</a>
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('seo.landing', ['listing' => 'sewa', 'type' => 'apartemen', 'city' => 'surabaya']) }}">Apartemen disewa di Surabaya</a>
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('seo.landing', ['listing' => 'jual', 'type' => 'rumah', 'city' => 'bandung']) }}">Rumah dijual di Bandung</a>
            <a class="px-3 py-2 rounded-lg border bg-background hover:underline" href="{{ route('properties.index') }}">Semua Properti</a>
        </div>
    </div>
</div>
@endsection
