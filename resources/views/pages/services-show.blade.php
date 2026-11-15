@extends('layouts.metronic')

@php
    $rawPhone = preg_replace('/\D+/', '', (string) $service->phone);
    if ($rawPhone !== '' && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    }
    $waHref = $rawPhone !== ''
        ? 'https://wa.me/' . $rawPhone . '?text=' . urlencode('Halo ' . $service->name . ', saya tertarik dengan jasa ' . $service->category . ' Anda yang terdaftar di Rumah123.')
        : null;
@endphp

@section('title', $service->name . ' — Jasa ' . ucfirst($service->category) . ' — Rumah123')

@section('content')
<div class="max-w-4xl mx-auto px-4 lg:px-6 py-8">
    <a href="{{ route('services.index') }}" class="text-sm text-primary hover:underline">&larr; Kembali ke direktori jasa</a>

    <div class="kt-card p-6 mt-3">
        <span class="text-[11px] font-semibold uppercase tracking-wide text-primary">{{ ucfirst($service->category) }}</span>
        <h1 class="text-2xl font-extrabold mt-1">{{ $service->name }}</h1>
        <div class="text-sm text-muted-foreground mt-1">{{ $service->city }} &middot; {{ $service->phone }}</div>

        @if ($service->description)
            <p class="text-sm text-muted-foreground mt-4 whitespace-pre-line">{{ $service->description }}</p>
        @endif

        <div class="flex flex-wrap gap-2 mt-5">
            @if ($waHref)
                <a href="{{ $waHref }}" target="_blank" rel="noopener" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-message-text"></i> Hubungi via WhatsApp
                </a>
                <a href="tel:{{ $rawPhone }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-phone"></i> {{ $service->phone }}
                </a>
            @else
                <span class="kt-btn kt-btn-outline opacity-50 pointer-events-none">
                    <i class="ki-filled ki-phone"></i> {{ $service->phone }}
                </span>
            @endif
        </div>
        <p class="text-xs text-muted-foreground mt-3">Kontak langsung penyedia jasa — tanpa perantara formulir lead (lead wajib terikat properti).</p>
    </div>
</div>
@endsection
