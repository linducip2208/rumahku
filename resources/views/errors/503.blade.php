@extends('layouts.metronic')

@section('title', 'Layanan Tidak Tersedia (503) — Rumah123')

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-16 text-center">
    <div class="mx-auto mb-6 grid place-items-center size-20 rounded-2xl bg-blue-50 border border-blue-100 text-4xl" aria-hidden="true">
        🔧
    </div>
    <p class="text-sm font-semibold text-blue-600 mb-2">Error 503</p>
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Layanan sedang perawatan</h1>
    <p class="text-muted-foreground mb-8">Kami sedang melakukan pemeliharaan. Coba kembali lagi dalam beberapa saat.</p>
    <div class="flex flex-wrap items-center justify-center gap-3 mb-10">
        <a href="{{ route('home') }}" class="kt-btn kt-btn-primary">Kembali ke Beranda</a>
        <button type="button" onclick="history.back()" class="kt-btn kt-btn-outline">Kembali ke Sebelumnya</button>
    </div>
</div>
@endsection
