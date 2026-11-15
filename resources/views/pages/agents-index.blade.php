@extends('layouts.metronic')

@section('title', 'Daftar Agen Terverifikasi — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Agen Terverifikasi</h1>
    <p class="text-sm text-muted-foreground mt-1">Hubungi agen profesional untuk kebutuhan propertimu.</p>
    <div class="mt-6">
        @if ($agents->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Belum ada agen terverifikasi.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($agents as $agent)
                    <a href="{{ route('agents.show', $agent->id) }}" class="kt-card p-5 hover:shadow-md transition flex items-center gap-3">
                        <span class="grid place-items-center size-12 rounded-full bg-primary/10 text-primary font-bold shrink-0">{{ strtoupper(substr(optional($agent->user)->name ?? 'A', 0, 1)) }}</span>
                        <span>
                            <span class="font-bold block">{{ optional($agent->user)->name ?? '-' }}</span>
                            <span class="text-xs text-muted-foreground block">{{ $agent->agency ?? 'Agen Independen' }} &middot; Terverifikasi</span>
                        </span>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $agents->links() }}</div>
        @endif
    </div>
</div>
@endsection
