@props(['property'])

@php
    $cover = $property->images->first()?->path ?? ($property->cover ?? null);
@endphp
<a href="{{ route('properties.show', $property->slug) }}" class="kt-card overflow-hidden hover:shadow-md transition">
    <div class="relative aspect-[4/3] bg-muted grid place-items-center text-muted-foreground text-sm">
        @if ($cover)
            <x-skeleton class="absolute inset-0 size-full !rounded-none" />
            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="relative size-full object-cover bg-muted animate-pulse" loading="lazy" onload="this.classList.remove('animate-pulse');this.previousElementSibling?.remove()" />
        @else
            <i class="ki-filled ki-home text-3xl"></i>
        @endif
        @if ($property->featured)
            <span class="absolute top-2 left-2 inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                <i class="ki-filled ki-star"></i> Unggulan
            </span>
        @endif
    </div>
    <div class="p-4">
        <span class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ ($property->listing_status ?? 'jual') === 'jual' ? 'Dijual' : 'Disewa' }} &middot; {{ ucfirst($property->type ?? '') }}</span>
        <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
        <div class="font-semibold mt-1 line-clamp-1">{{ $property->title }}</div>
        <div class="text-xs text-muted-foreground mt-1">
            {{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; LB {{ $property->building_area ?? '-' }}m² &middot; {{ $property->city }}
        </div>
    </div>
</a>
