@extends('layouts.metronic')

@section('title', 'Lelang Properti — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Lelang Properti</h1>
    <p class="text-sm text-muted-foreground mt-1">Daftar lelang berstatus <span class="font-semibold">open</span>. Klik Lihat &amp; Bid untuk menawar.</p>

    @if ($auctions->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada lelang terbuka saat ini.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-6">
            @foreach ($auctions as $auction)
                <div class="kt-card p-5">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Lelang #{{ $auction->id }} &middot; {{ $auction->status }}</div>
                    <div class="font-bold mt-1 line-clamp-1">{{ $auction->property?->title ?? 'Properti #' . $auction->property_id }}</div>
                    <div class="text-xs text-muted-foreground">{{ $auction->property?->city ?? '-' }}</div>
                    <div class="mt-3 text-sm grid gap-1">
                        <div>Harga awal: <span class="font-semibold">Rp {{ number_format($auction->start_price, 0, ',', '.') }}</span></div>
                        <div>Harga saat ini: <span class="font-extrabold text-primary">Rp {{ number_format($auction->current_price ?? $auction->start_price, 0, ',', '.') }}</span></div>
                        <div class="text-xs text-muted-foreground">Berakhir: {{ $auction->ends_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                    <a href="{{ route('auctions.show', $auction) }}" class="kt-btn kt-btn-primary w-full justify-center mt-3">Lihat &amp; Bid</a>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $auctions->links() }}</div>
    @endif
</div>
@endsection
