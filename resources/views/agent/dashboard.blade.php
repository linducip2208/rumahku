@extends('layouts.metronic')

@section('title', 'Dasbor Agen — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">Dasbor Agen</h1>
            <p class="text-sm text-muted-foreground mt-1">Halo, {{ auth()->user()->name }}. Pantau performa listing Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('agent.orders.index') }}" class="kt-btn kt-btn-outline">Order Saya</a>
            <a href="{{ route('agent.listings.create') }}" class="kt-btn kt-btn-primary"><i class="ki-filled ki-plus"></i> Tambah Listing</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-6">
        <div class="kt-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Total Listing</div>
            <div class="text-3xl font-extrabold mt-1">{{ number_format($totalListings) }}</div>
            <a href="{{ route('agent.listings.index') }}" class="text-sm text-primary hover:underline mt-2 inline-block">Kelola listing →</a>
        </div>
        <div class="kt-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Total Dilihat</div>
            <div class="text-3xl font-extrabold mt-1">{{ number_format($totalViews) }}</div>
            <div class="text-sm text-muted-foreground mt-2">kali properti dilihat</div>
        </div>
        <div class="kt-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Total Leads</div>
            <div class="text-3xl font-extrabold mt-1">{{ number_format($totalLeads) }}</div>
            <div class="text-sm text-muted-foreground mt-2">calon pembeli menghubungi</div>
        </div>
        <div class="kt-card p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Order Pending</div>
            <div class="text-3xl font-extrabold mt-1">{{ number_format($pendingOrders) }}</div>
            <a href="{{ route('agent.orders.index') }}" class="text-sm text-primary hover:underline mt-2 inline-block">Lihat order →</a>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2 mt-6">
        <div class="kt-card p-5">
            <h2 class="font-bold mb-3">Listing Terbaru</h2>
            @if ($latestProperties->isEmpty())
                <p class="text-sm text-muted-foreground">Belum ada listing. <a href="{{ route('agent.listings.create') }}" class="text-primary hover:underline font-semibold">Buat listing pertama</a>.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($latestProperties as $property)
                        <li class="py-2 flex items-center justify-between gap-3 text-sm">
                            <span class="font-medium line-clamp-1">{{ $property->title }}</span>
                            <span class="text-muted-foreground shrink-0">{{ number_format($property->views) }} views</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="kt-card p-5">
            <h2 class="font-bold mb-3">Leads Terbaru</h2>
            @if ($latestLeads->isEmpty())
                <p class="text-sm text-muted-foreground">Belum ada leads masuk.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach ($latestLeads as $lead)
                        <li class="py-2 text-sm">
                            <span class="font-medium">{{ $lead->name }}</span>
                            <span class="text-muted-foreground">· {{ $lead->phone }} · {{ $lead->property?->title ?? '-' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>
@endsection
