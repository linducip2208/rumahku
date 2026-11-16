@extends('layouts.metronic')

@section('title', 'Daftar Agen Terverifikasi — Rumah123')

@push('styles')
    <x-seo-meta
        title="Daftar Agen Terverifikasi — Rumah123"
        description="Temukan agen properti terverifikasi di Indonesia untuk membantu jual, sewa, dan cari hunian impianmu."
        :canonical="route('agents.index')"
    />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold tracking-tight">Agen Terverifikasi</h1>
    <p class="text-sm text-muted-foreground mt-1">Hubungi agen profesional untuk kebutuhan propertimu.</p>
    <div class="mt-6">
        @if ($agents->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Belum ada agen terverifikasi.
            </div>
        @else
            <p class="text-xs text-muted-foreground mb-3">{{ $agents->total() }} agen siap membantu</p>
            <div class="grid gap-3 sm:gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($agents as $agent)
                    <a href="{{ route('agents.show', $agent->id) }}" class="kt-card p-5 hover:shadow-md transition flex items-center gap-3">
                        <span class="grid place-items-center size-12 rounded-full bg-primary/10 text-primary font-bold shrink-0">{{ strtoupper(substr(optional($agent->user)->name ?? 'A', 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="font-bold block truncate">{{ optional($agent->user)->name ?? '-' }}</span>
                            <span class="text-xs text-muted-foreground block truncate">{{ $agent->agency ?? 'Agen Independen' }} &middot; Terverifikasi</span>
                        </span>
                    </a>
                @endforeach
            </div>
            <div class="mt-6 flex justify-center">{{ $agents->links() }}</div>
        @endif
    </div>
</div>
@endsection
