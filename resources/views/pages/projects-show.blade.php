@extends('layouts.metronic')

@section('title', ($project->name ?? 'Detail Proyek') . ' — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground mb-2">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('projects.index') }}" class="hover:underline">Proyek</a> /
        <span>{{ $project->name }}</span>
    </nav>
    <h1 class="text-2xl lg:text-3xl font-extrabold">{{ $project->name }}</h1>
    <p class="text-sm text-muted-foreground mt-1">{{ $project->city }} &middot; Developer: {{ optional($project->developer)->name ?? '-' }}</p>
    <div class="text-xl font-extrabold text-primary mt-3">Mulai Rp {{ number_format($project->price_from, 0, ',', '.') }}</div>
    <div class="kt-card p-5 mt-4">
        <h2 class="font-bold mb-2">Tentang Proyek</h2>
        <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $project->description }}</div>
    </div>

    @php
        $unitTypes = is_array($project->unit_types) ? array_values(array_filter($project->unit_types, fn ($u) => is_array($u) && ($u['nama'] ?? '') !== '')) : [];
        $projectFacilities = array_values(array_filter(array_map('trim', explode(',', (string) ($project->facilities ?? ''))), fn ($f) => $f !== ''));
    @endphp

    @if (! empty($unitTypes))
        <div class="kt-card p-5 mt-4 overflow-hidden">
            <h2 class="font-bold mb-3">Unit &amp; Tipe</h2>
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full text-sm min-w-[640px]">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted-foreground border-b border-border">
                            <th class="py-2 pr-3 font-semibold">Tipe</th>
                            <th class="py-2 pr-3 font-semibold">LT</th>
                            <th class="py-2 pr-3 font-semibold">LB</th>
                            <th class="py-2 pr-3 font-semibold">KT</th>
                            <th class="py-2 pr-3 font-semibold">KM</th>
                            <th class="py-2 font-semibold text-right">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unitTypes as $unit)
                            <tr class="border-b border-border/60 last:border-0">
                                <td class="py-2.5 pr-3 font-semibold">{{ $unit['nama'] }}</td>
                                <td class="py-2.5 pr-3 text-muted-foreground">{{ $unit['lt'] ?? '-' }}{{ isset($unit['lt']) ? ' m²' : '' }}</td>
                                <td class="py-2.5 pr-3 text-muted-foreground">{{ $unit['lb'] ?? '-' }}{{ isset($unit['lb']) ? ' m²' : '' }}</td>
                                <td class="py-2.5 pr-3 text-muted-foreground">{{ $unit['kt'] ?? '-' }}</td>
                                <td class="py-2.5 pr-3 text-muted-foreground">{{ $unit['km'] ?? '-' }}</td>
                                <td class="py-2.5 text-right font-extrabold text-primary whitespace-nowrap">{{ isset($unit['harga']) ? 'Rp ' . number_format((float) $unit['harga'], 0, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if (! empty($projectFacilities))
        <div class="kt-card p-5 mt-4">
            <h2 class="font-bold mb-3">Fasilitas Proyek</h2>
            <ul class="grid gap-2 sm:grid-cols-2 text-sm">
                @foreach ($projectFacilities as $facility)
                    <li class="flex items-start gap-2 text-muted-foreground">
                        <i class="ki-filled ki-check-circle text-primary mt-0.5"></i>
                        <span>{{ $facility }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($properties->isNotEmpty())
        <h2 class="font-bold mt-8 mb-3">Unit & Listing Developer Terkait</h2>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    @endif
</div>
@endsection
