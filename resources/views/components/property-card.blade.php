@props(['property'])

@php
    $cover = $property->images->first()?->path ?? ($property->cover ?? null);
@endphp
<a href="{{ route('properties.show', $property->slug) }}" class="group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
    <div class="relative aspect-[4/3] bg-slate-100 grid place-items-center text-slate-400 text-sm overflow-hidden">
        @if ($cover)
            <x-skeleton class="absolute inset-0 size-full !rounded-none" />
            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="relative size-full object-cover bg-slate-100 animate-pulse transition duration-500 group-hover:scale-105" loading="lazy" onload="this.classList.remove('animate-pulse');this.previousElementSibling?.remove()" />
        @else
            <i class="ki-filled ki-home text-3xl"></i>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/35 to-transparent pointer-events-none" aria-hidden="true"></div>
        @if ($property->featured)
            <span class="absolute top-2 left-2 inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                <i class="ki-filled ki-star"></i> Unggulan
            </span>
        @endif
        <span class="absolute bottom-2 left-2 text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full {{ ($property->listing_status ?? 'jual') === 'jual' ? 'bg-blue-600/90 text-white' : 'bg-emerald-600/90 text-white' }}">
            {{ ($property->listing_status ?? 'jual') === 'jual' ? 'Dijual' : 'Disewa' }}
        </span>
    </div>
    <div class="p-4">
        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ ucfirst($property->type ?? '') }} &middot; {{ $property->city }}</span>
        <div class="text-blue-700 font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
        <div class="font-semibold mt-1 line-clamp-1 text-[#0F2A4A] group-hover:text-blue-700">{{ $property->title }}</div>
        <div class="text-xs text-slate-500 mt-1">
            {{ $property->bedrooms ?? '-' }} KT &middot; {{ $property->bathrooms ?? '-' }} KM &middot; LT {{ $property->land_area ?? '-' }}m² &middot; LB {{ $property->building_area ?? '-' }}m²
        </div>
    </div>
</a>
