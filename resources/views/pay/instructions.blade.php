@extends('layouts.metronic')

@section('title', 'Instruksi Pembayaran #' . $order->id)

@section('content')
<div class="max-w-2xl mx-auto px-4 lg:px-6 py-10">
    <h1 class="text-2xl font-extrabold tracking-tight">Instruksi Pembayaran</h1>
    <p class="text-sm text-muted-foreground mt-1">Order #{{ $order->id }} — Paket {{ $order->package_slug }}</p>

    <div class="mt-6 rounded-xl border border-border bg-card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-muted-foreground">Nominal</span>
            <span class="text-xl font-extrabold">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-muted-foreground">Kode referensi</span>
            <code class="text-sm font-mono bg-muted px-2 py-1 rounded">{{ $charge['reference_id'] }}</code>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-muted-foreground">Status</span>
            @if ($order->status === 'paid')
                <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-semibold">LUNAS</span>
            @else
                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">MENUNGGU PEMBAYARAN</span>
            @endif
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-border bg-card p-6">
        <h2 class="font-bold mb-2">Cara membayar</h2>
        <p class="text-sm whitespace-pre-line">{{ $charge['raw']['instructions'] ?? 'Ikuti instruksi dari penyedia pembayaran.' }}</p>

        @if (! empty($charge['raw']['bank_accounts']))
            <h3 class="font-semibold mt-4 mb-1 text-sm">Rekening tujuan</h3>
            <pre class="text-sm whitespace-pre-line bg-muted rounded-lg p-4">{{ $charge['raw']['bank_accounts'] }}</pre>
        @endif

        <p class="text-xs text-muted-foreground mt-4">
            Cantumkan kode referensi pada berita transfer. Setelah pembayaran terverifikasi,
            status order berubah menjadi lunas otomatis.
        </p>
    </div>
</div>
@endsection
