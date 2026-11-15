@extends('layouts.metronic')

@section('title', 'Akses Ditolak (403) — Rumah123')

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-16 text-center">
    <div class="mx-auto mb-6 grid place-items-center size-20 rounded-2xl bg-blue-50 border border-blue-100 text-4xl" aria-hidden="true">
        🚫
    </div>
    <p class="text-sm font-semibold text-blue-600 mb-2">Error 403</p>
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">Akses ditolak</h1>
    <p class="text-muted-foreground mb-8">Maaf, kamu tidak punya izin untuk membuka halaman ini. Coba masuk dengan akun yang sesuai atau kembali ke beranda.</p>
    <div class="flex flex-wrap items-center justify-center gap-3 mb-10">
        <a href="{{ route('home') }}" class="kt-btn kt-btn-primary">Kembali ke Beranda</a>
        <button type="button" onclick="history.back()" class="kt-btn kt-btn-outline">Kembali ke Sebelumnya</button>
    </div>
</div>
@endsection
