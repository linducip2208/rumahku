@extends('layouts.metronic')

@section('title', 'Harga Paket Iklan — Rumah123')

@push('styles')
    <x-seo-meta
        title="Harga Paket Iklan — Rumah123"
        description="Pilih paket iklan properti Rumah123: tingkatkan visibilitas listing dengan slot Unggulan dan badge khusus."
        :canonical="route('pricing')"
    />
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-12">
    <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight">Pilih Paket Iklan Properti</h1>
        <p class="text-muted-foreground mt-2">Tingkatkan visibilitas listing Anda. Paket berbayar menempatkan properti di posisi Unggulan.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4 mt-10">
        @forelse ($packages ?? [] as $package)
            <div class="kt-card p-6 flex flex-col gap-4 {{ $package->featured_included ? 'border-primary ring-1 ring-primary' : '' }}">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">{{ $package->name }}</h2>
                    @if ($package->badge)
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-primary text-primary-foreground">{{ $package->badge }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-2xl font-extrabold text-primary">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                    <span class="text-xs text-muted-foreground">/ {{ $package->duration_days }} hari</span>
                </div>
                <ul class="text-sm text-muted-foreground space-y-2">
                    <li>Maks. {{ $package->max_photos }} foto</li>
                    <li>{{ $package->featured_included ? 'Termasuk badge & slot Unggulan' : 'Listing standar' }}</li>
                    <li>Tayang {{ $package->duration_days }} hari</li>
                </ul>
                <form method="POST" action="{{ route('orders.store') }}" class="mt-auto">
                    @csrf
                    <input type="hidden" name="package_slug" value="{{ $package->slug }}" />
                    <input type="hidden" name="property_id" value="{{ request('property_id') }}" />
                    <button type="submit" class="kt-btn {{ $package->featured_included ? 'kt-btn-primary' : 'kt-btn-outline' }} w-full justify-center">
                        Pilih {{ $package->name }}
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Paket belum tersedia saat ini.
            </div>
        @endforelse
    </div>

    <p class="text-xs text-muted-foreground text-center mt-8">Pembayaran diverifikasi admin. Setelah lunas, properti paket Unggulan otomatis tampil sebagai featured.</p>
</section>
@endsection
