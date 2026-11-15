@extends('layouts.metronic')

@section('title', 'Order Saya — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">Order Saya</h1>
    <p class="text-sm text-muted-foreground mt-1">Riwayat pembelian paket unggulan untuk listing Anda.</p>

    @if ($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada order. Pilih paket di halaman <a href="{{ route('pricing') }}" class="text-primary hover:underline font-semibold">harga</a>.
        </div>
    @else
        <div class="kt-card mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted-foreground border-b border-border">
                        <th class="px-4 py-3 font-semibold">Properti</th>
                        <th class="px-4 py-3 font-semibold">Paket</th>
                        <th class="px-4 py-3 font-semibold">Nominal</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Info Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $order->property?->title ?? ('#' . $order->property_id) }}</td>
                            <td class="px-4 py-3">{{ $order->package_slug }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : ($order->status === 'expired' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $order->status === 'paid' ? 'Lunas' : ($order->status === 'expired' ? 'Kedaluwarsa' : 'Pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                @if ($order->status === 'pending')
                                    Transfer ke BCA 123-456-7890 a.n. Rumah123 sebesar nominal di atas, lalu tunggu verifikasi admin.
                                @elseif ($order->status === 'paid')
                                    Dibayar{{ $order->paid_at ? ' pada ' . $order->paid_at->format('d M Y') : '' }}.
                                @else
                                    Order kedaluwarsa.
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</section>
@endsection
