{{-- Widget rekomendasi AI-stub: relasi kota+tipe+pita harga, tanpa API eksternal. --}}
@props(['recommendations'])

@if (isset($recommendations) && $recommendations->isNotEmpty())
    <h2 class="font-bold mt-8 mb-3">Rekomendasi Untukmu</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($recommendations as $rec)
            <x-property-card :property="$rec" />
        @endforeach
    </div>
@elseif (! isset($recommendations))
    {{-- Dipakai sebagai @include tanpa data: aman, tidak render apa-apa. --}}
@endif
