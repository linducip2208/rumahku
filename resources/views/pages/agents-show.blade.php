@extends('layouts.metronic')

@section('title', ((optional($agent->user)->name) ?? 'Agen') . ' — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground mb-2">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('agents.index') }}" class="hover:underline">Agen</a> /
        <span>{{ optional($agent->user)->name }}</span>
    </nav>
    <div class="kt-card p-5 flex flex-wrap items-center gap-4">
        <span class="grid place-items-center size-16 rounded-full bg-primary/10 text-primary font-bold text-xl">{{ strtoupper(substr(optional($agent->user)->name ?? 'A', 0, 1)) }}</span>
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-extrabold">{{ optional($agent->user)->name }}</h1>
            <p class="text-sm text-muted-foreground">{{ $agent->agency ?? 'Agen Independen' }} &middot; Terverifikasi sejak {{ optional($agent->verified_at)->format('d M Y') }}</p>
            <p class="text-sm mt-1">Telp: {{ $agent->phone ?? '-' }} &middot; WA: {{ $agent->wa ?? '-' }}</p>
        </div>
        @php
            $rawAgentPhone = $agent->wa ?? $agent->phone ?? null;
            $agentWa = $rawAgentPhone ? preg_replace('/\D+/', '', (string) $rawAgentPhone) : null;
            if ($agentWa && str_starts_with($agentWa, '0')) {
                $agentWa = '62' . substr($agentWa, 1);
            }
        @endphp
        @if ($agentWa)
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="tel:{{ $agentWa }}" class="kt-btn kt-btn-outline flex-1 sm:flex-none"><i class="ki-filled ki-phone"></i> Telepon</a>
                <a href="https://wa.me/{{ $agentWa }}?text={{ urlencode('Halo ' . (optional($agent->user)->name ?? 'Agen') . ', saya menemukan profil Anda di Rumah123 dan ingin bertanya tentang properti.') }}" target="_blank" rel="noopener" class="kt-btn kt-btn-primary flex-1 sm:flex-none"><i class="ki-filled ki-message-text"></i> Hubungi via WhatsApp</a>
            </div>
        @endif
    </div>

    <h2 class="font-bold mt-8 mb-3">Listing Agen ({{ $properties->total() }})</h2>
    @if ($properties->total() === 0)
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
            Agen ini belum memiliki listing aktif.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
        <div class="mt-6">{{ $properties->links() }}</div>
    @endif
</div>
@endsection
