@extends('layouts.metronic')

@section('title', 'Bandingkan Properti — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold tracking-tight">Bandingkan Properti</h1>
    <p class="text-sm text-muted-foreground mt-1">Maksimal 4 properti. Buka halaman detail properti lalu tekan tombol &ldquo;Bandingkan +&rdquo; untuk menambahkannya ke daftar ini.</p>

    @if ($properties->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada properti untuk dibandingkan. Jelajahi <a href="{{ route('properties.index') }}" class="text-primary hover:underline font-semibold">daftar properti</a>, buka halamannya, lalu tekan tombol &ldquo;Bandingkan +&rdquo;.
        </div>
    @else
        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm kt-card overflow-hidden">
                <thead>
                    <tr class="border-b border-border text-left">
                        <th class="p-4 w-40 text-muted-foreground font-medium">Aspek</th>
                        @foreach ($properties as $property)
                            <th class="p-4 min-w-52 align-top">
                                <a href="{{ route('properties.show', $property->slug) }}" class="font-bold hover:underline line-clamp-2">{{ $property->title }}</a>
                                <div class="text-xs text-muted-foreground font-normal">{{ $property->city }}</div>
                                <form method="POST" action="{{ route('compare.remove', $property) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-destructive hover:underline">Hapus dari perbandingan</button>
                                </form>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Harga</td>
                        @foreach ($properties as $property)
                            <td class="p-4 font-extrabold text-primary">Rp {{ number_format($property->price, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Status / Tipe</td>
                        @foreach ($properties as $property)
                            <td class="p-4">{{ $property->listing_status === 'jual' ? 'Dijual' : 'Disewa' }} &middot; {{ ucfirst($property->type) }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Kamar Tidur</td>
                        @foreach ($properties as $property)
                            <td class="p-4 font-semibold">{{ $property->bedrooms ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Kamar Mandi</td>
                        @foreach ($properties as $property)
                            <td class="p-4 font-semibold">{{ $property->bathrooms ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Luas Tanah</td>
                        @foreach ($properties as $property)
                            <td class="p-4">{{ $property->land_area ?? '-' }} m²</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-border">
                        <td class="p-4 text-muted-foreground">Luas Bangunan</td>
                        @foreach ($properties as $property)
                            <td class="p-4">{{ $property->building_area ?? '-' }} m²</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-4 text-muted-foreground">Kota</td>
                        @foreach ($properties as $property)
                            <td class="p-4">{{ $property->city }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    @include('components.recommendations', ['recommendations' => $recommendations ?? collect()])
</div>
@endsection
