@extends('layouts.metronic')

@php
    $current = $auction->current_price ?? $auction->start_price;
    $minimum = $auction->minimumNextBid();
@endphp

@section('title', 'Lelang #' . $auction->id . ' — ' . ($auction->property?->title ?? 'Properti') . ' — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8 grid gap-6 lg:grid-cols-[1fr_340px]">
    <div>
        <a href="{{ url('/lelang') }}" class="text-sm text-primary hover:underline">&larr; Kembali ke daftar lelang</a>
        <h1 class="text-2xl font-extrabold mt-2">{{ $auction->property?->title ?? 'Properti #' . $auction->property_id }}</h1>
        <div class="text-sm text-muted-foreground mt-1">
            {{ $auction->property?->address ?? '' }}
            {{ $auction->property?->city ? '— ' . $auction->property->city : '' }}
        </div>

        <div class="kt-card p-5 mt-4">
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-wide">
                <span class="px-2 py-1 rounded bg-primary/10 text-primary">Lelang #{{ $auction->id }}</span>
                <span class="px-2 py-1 rounded bg-muted text-muted-foreground">{{ ucfirst($auction->status) }}</span>
            </div>
            <div class="grid sm:grid-cols-3 gap-3 mt-4 text-sm">
                <div class="rounded-xl bg-muted/60 p-3">
                    <div class="text-muted-foreground text-xs">Harga awal</div>
                    <div class="font-bold">Rp {{ number_format($auction->start_price, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl bg-muted/60 p-3">
                    <div class="text-muted-foreground text-xs">Harga saat ini</div>
                    <div class="font-extrabold text-primary">Rp {{ number_format($current, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-xl bg-muted/60 p-3">
                    <div class="text-muted-foreground text-xs">Tawaran minimal berikutnya</div>
                    <div class="font-bold">Rp {{ number_format($minimum, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="text-xs text-muted-foreground mt-3">
                Mulai: {{ $auction->starts_at?->format('d M Y H:i') ?? '-' }}
                &middot; Berakhir: <span id="auction-ends-at" data-ends-at="{{ $auction->ends_at?->toIso8601String() }}">{{ $auction->ends_at?->format('d M Y H:i') ?? '-' }}</span>
                &middot; <span id="auction-countdown" class="font-semibold text-foreground">—</span>
            </div>
        </div>

        <div class="kt-card p-5 mt-4">
            <h2 class="font-bold mb-3">Riwayat Tawaran ({{ $bids->count() }} terbaru)</h2>
            @if ($bids->isEmpty())
                <p class="text-sm text-muted-foreground">Belum ada tawaran. Jadilah penawar pertama!</p>
            @else
                <ol class="grid gap-2 text-sm">
                    @foreach ($bids as $bid)
                        <li class="flex items-center justify-between rounded-lg bg-muted/60 px-3 py-2">
                            <span class="font-medium">{{ $bid->user?->name ?? 'Anonim' }}</span>
                            <span class="flex items-center gap-3">
                                <span class="font-extrabold">Rp {{ number_format($bid->amount, 0, ',', '.') }}</span>
                                <span class="text-xs text-muted-foreground">{{ $bid->created_at?->format('d M Y H:i') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    <aside>
        <div class="kt-card p-5 lg:sticky lg:top-20">
            <h2 class="font-bold">Pasang Tawaran</h2>
            <p class="text-xs text-muted-foreground mt-1">Minimal Rp {{ number_format($minimum, 0, ',', '.') }} (kelipatan validasi 1% / Rp 1 jt).</p>

            @if ($errors->has('amount'))
                <div class="mt-3 rounded-lg bg-destructive/10 text-destructive text-sm px-3 py-2">{{ $errors->first('amount') }}</div>
            @endif

            @auth
                <form method="POST" action="{{ url('/lelang/' . $auction->id . '/bid') }}" class="mt-3 grid gap-2">
                    @csrf
                    <label class="text-xs">Nominal tawaran (Rp)
                        <input type="number" name="amount" required min="{{ $minimum }}" step="1000" value="{{ old('amount', $minimum) }}" class="kt-input mt-1" />
                    </label>
                    <button type="submit" class="kt-btn kt-btn-primary w-full">Kirim Tawaran</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="kt-btn kt-btn-primary w-full mt-3">Masuk untuk menawar</a>
            @endauth
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var el = document.getElementById('auction-ends-at');
    var out = document.getElementById('auction-countdown');
    if (!el || !out || !el.dataset.endsAt) { if (out) out.textContent = '—'; return; }
    var ends = new Date(el.dataset.endsAt).getTime();
    function tick() {
        var diff = ends - Date.now();
        if (diff <= 0) { out.textContent = 'Lelang berakhir'; return; }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor(diff % 86400000 / 3600000);
        var m = Math.floor(diff % 3600000 / 60000);
        var s = Math.floor(diff % 60000 / 1000);
        out.textContent = 'Sisa ' + d + 'h ' + h + 'j ' + m + 'm ' + s + 'd';
        setTimeout(tick, 1000);
    }
    tick();
})();
</script>
@endpush
