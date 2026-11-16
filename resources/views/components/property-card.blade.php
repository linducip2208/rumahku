@props(['property'])

@php
    $cover = $property->images->first()?->path ?? ($property->cover ?? null);
    $isJual = ($property->listing_status ?? 'jual') === 'jual';
    $detailUrl = route('properties.show', $property->slug);
    $priceLabel = 'Rp ' . number_format($property->price, 0, ',', '.') . ($isJual ? '' : '/thn');
@endphp
<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
    <a href="{{ $detailUrl }}" class="absolute inset-0 z-10" aria-label="{{ $property->title }}"></a>
    <div class="relative m-2 aspect-[4/3] overflow-hidden rounded-xl bg-slate-100">
        @if ($cover)
            <img src="{{ asset('storage/'.$cover) }}" alt="{{ $property->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105" />
        @else
            <div class="grid size-full place-items-center text-slate-300">
                <i class="ki-filled ki-home text-4xl"></i>
            </div>
        @endif
        <div class="absolute left-3 top-3 flex flex-wrap items-center gap-1.5">
            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide shadow {{ $isJual ? 'bg-blue-600 text-white' : 'bg-emerald-600 text-white' }}">
                {{ $isJual ? 'Dijual' : 'Disewa' }}
            </span>
            @if ($property->featured)
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-400 px-2.5 py-1 text-[11px] font-bold text-amber-950 shadow">
                    <i class="ki-filled ki-star text-xs"></i> Unggulan
                </span>
            @endif
        </div>
        <form method="POST" action="{{ route('favorites.toggle', $property) }}" class="absolute right-3 top-3 z-20" onclick="event.stopPropagation()">
            @csrf
            <button type="submit" onclick="event.stopPropagation()" aria-label="Simpan ke favorit" title="Simpan ke favorit" class="grid size-8 place-items-center rounded-full bg-white/95 text-slate-400 shadow transition hover:scale-105 hover:text-rose-600">
                <i class="ki-filled ki-heart text-base"></i>
            </button>
        </form>
        <span class="absolute bottom-3 left-3 rounded-full bg-white px-3 py-1 text-sm font-extrabold text-slate-900 shadow-md">
            {{ $priceLabel }}
        </span>
    </div>
    <div class="flex flex-1 flex-col px-4 pb-4 pt-1">
        <h3 class="truncate text-[15px] font-semibold text-slate-900">{{ $property->title }}</h3>
        <p class="mt-1 flex items-center gap-1 truncate text-sm text-slate-500">
            <i class="ki-filled ki-geolocation text-sm"></i>
            <span class="truncate">{{ $property->district ? $property->district.', ' : '' }}{{ $property->city }}</span>
        </p>
        <div class="mt-3 grid grid-cols-3 gap-2 border-t border-slate-100 pt-3">
            <div class="min-w-0">
                <div class="text-xs text-slate-500">Luas Bangunan</div>
                <div class="truncate text-[13px] font-semibold text-slate-800">{{ $property->building_area ?? '-' }}{{ $property->building_area ? ' m²' : '' }}</div>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-slate-500">K. Tidur</div>
                <div class="truncate text-[13px] font-semibold text-slate-800">{{ $property->bedrooms ?? '-' }}{{ $property->bedrooms ? ' KT' : '' }}</div>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-slate-500">K. Mandi</div>
                <div class="truncate text-[13px] font-semibold text-slate-800">{{ $property->bathrooms ?? '-' }}{{ $property->bathrooms ? ' KM' : '' }}</div>
            </div>
        </div>
        <p class="mt-2 truncate text-xs text-slate-400">LT {{ $property->land_area ?? '-' }}{{ $property->land_area ? ' m²' : '' }} &middot; {{ ucfirst($property->type ?? '') }}</p>
    </div>
</article>
