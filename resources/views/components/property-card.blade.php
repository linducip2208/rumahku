@props(['property'])

@php
    $cover = $property->images->first()?->path ?? ($property->cover ?? null);
    $isJual = ($property->listing_status ?? 'jual') === 'jual';
    $detailUrl = route('properties.show', $property->slug);
@endphp
<article class="group relative flex flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
    <a href="{{ $detailUrl }}" class="absolute inset-0 z-10" aria-label="{{ $property->title }}"></a>
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        @if ($cover)
            <img src="{{ asset('storage/'.$cover) }}" alt="{{ $property->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105" />
        @else
            <div class="grid size-full place-items-center text-slate-300">
                <i class="ki-filled ki-home text-4xl"></i>
            </div>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-black/30 to-transparent" aria-hidden="true"></div>
        <span class="absolute left-2 top-2 rounded-full px-2 py-1 text-[11px] font-bold uppercase tracking-wide {{ $isJual ? 'bg-blue-600/95 text-white' : 'bg-emerald-600/95 text-white' }}">
            {{ $isJual ? 'Dijual' : 'Disewa' }}
        </span>
        @if ($property->featured)
            <span class="absolute bottom-2 left-2 inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-100/95 px-2 py-0.5 text-[11px] font-semibold text-amber-800">
                <i class="ki-filled ki-star text-xs"></i> Unggulan
            </span>
        @endif
        <form method="POST" action="{{ route('favorites.toggle', $property) }}" class="absolute right-2 top-2 z-20">
            @csrf
            <button type="submit" aria-label="Simpan ke favorit" class="grid size-8 place-items-center rounded-full bg-white/95 text-slate-400 shadow transition hover:text-rose-600">
                <i class="ki-filled ki-heart text-base"></i>
            </button>
        </form>
    </div>
    <div class="flex flex-1 flex-col p-3">
        <div class="text-lg font-extrabold leading-tight text-blue-700">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
        <h3 class="mt-0.5 truncate text-sm font-semibold text-[#0F2A4A]">{{ $property->title }}</h3>
        <p class="mt-0.5 flex items-center gap-1 truncate text-xs text-slate-500">
            <i class="ki-filled ki-geolocation text-xs"></i>
            <span class="truncate">{{ $property->district ? $property->district.', ' : '' }}{{ $property->city }}</span>
        </p>
        <p class="mt-2 flex flex-wrap gap-x-3 gap-y-0.5 border-t border-slate-100 pt-2 text-xs text-slate-600">
            <span><span class="font-bold text-[#0F2A4A]">{{ $property->bedrooms ?? '-' }}</span> KT</span>
            <span><span class="font-bold text-[#0F2A4A]">{{ $property->bathrooms ?? '-' }}</span> KM</span>
            <span>LT <span class="font-bold text-[#0F2A4A]">{{ $property->land_area ?? '-' }}</span>m²</span>
            <span>LB <span class="font-bold text-[#0F2A4A]">{{ $property->building_area ?? '-' }}</span>m²</span>
        </p>
    </div>
</article>
