{{-- Widget rekomendasi AI-stub: relasi kota+tipe+pita harga, tanpa API eksternal. --}}
@props(['recommendations'])

@if (isset($recommendations) && $recommendations->isNotEmpty())
    <h2 class="font-bold mt-8 mb-3">Rekomendasi Untukmu</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($recommendations as $rec)
            <a href="{{ route('properties.show', $rec->slug) }}" class="kt-card p-4 hover:shadow-md transition">
                <div class="text-primary font-extrabold text-sm">Rp {{ number_format($rec->price, 0, ',', '.') }}</div>
                <div class="font-semibold text-sm mt-1 line-clamp-1">{{ $rec->title }}</div>
                <div class="text-xs text-muted-foreground">{{ $rec->city }} &middot; {{ ucfirst($rec->type) }}</div>
            </a>
        @endforeach
    </div>
@elseif (! isset($recommendations))
    {{-- Dipakai sebagai @include tanpa data: aman, tidak render apa-apa. --}}
@endif
