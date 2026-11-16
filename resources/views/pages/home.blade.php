@extends('layouts.metronic')

@section('title', 'Rumah123 — Jual, Sewa & Proyek Baru')

@push('styles')
    <x-seo-meta
        title="Rumah123 — Jual, Sewa & Proyek Baru"
        description="Rumah123 — Portal properti Indonesia: cari rumah, apartemen, tanah, ruko, villa dijual & disewa, proyek baru, agen, dan simulasi KPR."
        :canonical="route('home')"
    />
@endpush

@php
    $landing = $landing ?? null;
    $landingGet = function (string $key, mixed $default = null) use ($landing): mixed {
        if ($landing === null) {
            return $default;
        }
        if (is_array($landing)) {
            return $landing[$key] ?? $default;
        }
        return $landing->{$key} ?? $default;
    };
    $show = function (string $key) use ($landingGet): bool {
        return (bool) $landingGet('show_'.$key, true);
    };
    $heroTitle = $landingGet('hero_title', 'Jual Beli dan Sewa Properti Jadi Mudah');
    $heroSubtitle = $landingGet('hero_subtitle', 'Cari rumah, apartemen, tanah, ruko, dan villa dijual & disewa di seluruh Indonesia.');
    $heroImage = $landingGet('hero_image');
    $heroFile = null;
    if (is_string($heroImage) && $heroImage !== '') {
        $candidate = str_starts_with($heroImage, 'landing/') ? $heroImage : 'landing/'.$heroImage;
        if (file_exists(public_path($candidate))) {
            $heroFile = $candidate;
        }
    }
    if ($heroFile === null && file_exists(public_path('landing/hero.jpg'))) {
        $heroFile = 'landing/hero.jpg';
    }

    $pix = fn (string $seed, int $w = 1200, int $h = 800): string => "https://picsum.photos/seed/{$seed}/{$w}/{$h}";

    $toCol = fn (mixed $v): \Illuminate\Support\Collection => $v instanceof \Illuminate\Support\Collection ? $v : collect($v ?? []);
    $featured = $toCol($featured ?? null);
    $latest = $toCol($latest ?? null);
    $cities = $toCol($cities ?? null);
    $agents = $toCol($agents ?? null);
    $articles = $toCol($articles ?? null);
    // Proyek Baru: hanya tampil bila controller mengirimkan data (field: name, slug, city, price_from, developer).
    $homeProjects = $toCol($projects ?? null);

    // ---- Kategori (8): controller => ['label','type','count','empty'] ----
    $typeLabels = ['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'tanah' => 'Tanah', 'villa' => 'Villa', 'ruko' => 'Ruko', 'kost' => 'Kost', 'gedung' => 'Gedung Komersial', 'gudang' => 'Gudang'];
    $rawCategories = $toCol($categories ?? null);
    if ($rawCategories->isNotEmpty()) {
        $categoryTiles = $rawCategories->map(function ($c) use ($typeLabels) {
            $c = is_array($c) ? (object) $c : $c;
            $type = $c->type ?? null;
            return [
                'name' => $c->label ?? $c->name ?? ($typeLabels[$type] ?? ucfirst((string) $type)),
                'type' => $type,
                'count' => $c->count ?? null,
                'empty' => (bool) ($c->empty ?? (($c->count ?? 0) === 0)),
            ];
        })->values();
    } else {
        $categoryTiles = collect($typeLabels)->map(fn ($label, $type) => ['name' => $label, 'type' => $type, 'count' => 0, 'empty' => true])->values();
    }

    // ---- Koleksi: controller => ['name','type','min_price','count'] (hanya yang berisi) ----
    $rawCollections = $toCol($collections ?? null);
    $collectionTiles = $rawCollections->values()->map(function ($c) {
        $c = is_array($c) ? (object) $c : $c;
        $params = array_filter(['type' => $c->type ?? null, 'min_price' => $c->min_price ?? null], fn ($v) => $v !== null && $v !== '');
        $label = $c->label ?? $c->name ?? '';
        $desc = isset($c->min_price) && $c->min_price
            ? 'Harga mulai Rp '.number_format((int) $c->min_price, 0, ',', '.')
            : ($c->desc ?? 'Pilihan '.mb_strtolower((string) $label));
        return ['label' => $label, 'desc' => $desc, 'count' => $c->count ?? null, 'params' => $params];
    })->filter(fn ($c) => $c['label'] !== '')->values();

    // ---- Kota ----
    $cityRows = $cities instanceof \Illuminate\Support\Collection ? $cities->take(8) : collect();
    $topCityNames = $cityRows->map(fn ($r) => is_array($r) ? ($r['city'] ?? '') : ($r->city ?? ''))->filter()->values();

    // ---- Popular searches: link REAL dari kombinasi + kota aktual ----
    $popularSearches = collect([
        ['label' => 'Rumah dijual', 'params' => ['status' => 'jual', 'type' => 'rumah']],
        ['label' => 'Apartemen disewa', 'params' => ['status' => 'sewa', 'type' => 'apartemen']],
        ['label' => 'Tanah dijual', 'params' => ['status' => 'jual', 'type' => 'tanah']],
        ['label' => 'Villa disewa', 'params' => ['status' => 'sewa', 'type' => 'villa']],
    ]);
    foreach ($topCityNames->take(4) as $cn) {
        $popularSearches->push(['label' => "Rumah dijual di {$cn}", 'params' => ['status' => 'jual', 'type' => 'rumah', 'city' => $cn]]);
    }
    $popularSearches = $popularSearches->take(8);

    $gradients = [
        'from-blue-700 via-blue-600 to-cyan-500',
        'from-[#0F2A4A] via-[#1B3A5F] to-blue-600',
        'from-emerald-700 via-emerald-600 to-teal-500',
        'from-amber-600 via-orange-500 to-rose-500',
        'from-violet-700 via-indigo-600 to-blue-500',
        'from-rose-700 via-rose-600 to-pink-500',
        'from-cyan-700 via-sky-600 to-blue-500',
        'from-slate-700 via-slate-600 to-slate-500',
    ];

    // ---- Akses cepat: semua link route() real yang ada ----
    $quickLinks = [
        ['label' => 'Cari Properti', 'icon' => 'ki-magnifier', 'url' => route('properties.index')],
        ['label' => 'Pasang Iklan', 'icon' => 'ki-plus', 'url' => route('agent.listings.create')],
        ['label' => 'Cari Agen', 'icon' => 'ki-people', 'url' => route('agents.index')],
        ['label' => 'Kalkulator KPR', 'icon' => 'ki-setting-2', 'url' => '#kpr'],
        ['label' => 'Lelang', 'icon' => 'ki-star', 'url' => route('auctions.index')],
        ['label' => 'Panduan', 'icon' => 'ki-document', 'url' => route('articles.index')],
        ['label' => 'Bandingkan', 'icon' => 'ki-arrows-horizontal', 'url' => route('compare.index')],
        ['label' => 'Proyek Baru', 'icon' => 'ki-home', 'url' => route('projects.index')],
    ];
@endphp

@section('content')
{{-- ============ 1. HERO KOMPAK + SEARCH CARD ============ --}}
@if ($show('hero'))
<section class="relative overflow-hidden bg-[#0F2A4A] text-white">
    @if ($heroFile)
        <img src="{{ asset($heroFile) }}" alt="Properti pilihan" fetchpriority="high" class="absolute inset-0 size-full object-cover" onerror="this.remove()" />
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#0F2A4A] via-[#1B3A5F] to-blue-700" aria-hidden="true"></div>
        <img src="{{ $pix('rumahku-hero', 1920, 900) }}" alt="" aria-hidden="true" loading="eager" fetchpriority="high" class="absolute inset-0 size-full object-cover" onerror="this.remove()" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-[#0F2A4A]/80 via-[#0F2A4A]/55 to-[#0F2A4A]/85" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <div class="flex min-h-[230px] flex-col justify-center py-8 md:min-h-[290px] md:py-10">
            <p class="inline-flex w-fit items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1.5 text-xs font-semibold backdrop-blur">
                <i class="ki-filled ki-home"></i> Portal Properti Indonesia
            </p>
            <h1 class="mt-3 max-w-2xl text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-4xl">{{ $heroTitle }}</h1>
            <p class="mt-2 max-w-xl text-sm text-white/85 sm:text-base">{{ $heroSubtitle }}</p>
        </div>
    </div>
</section>
<div class="relative z-10 mx-auto -mt-10 max-w-7xl px-4 lg:px-6">
    <div x-data="{ tab: 'jual' }" class="rounded-xl bg-white p-4 shadow-xl sm:p-5">
        <div class="flex gap-2 text-sm font-semibold" role="tablist" aria-label="Status listing">
            <button type="button" role="tab" :aria-selected="tab === 'jual'" @click="tab = 'jual'"
                :class="tab === 'jual' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="rounded-full px-4 py-2 transition">{{ __('site.nav.jual') }}</button>
            <button type="button" role="tab" :aria-selected="tab === 'sewa'" @click="tab = 'sewa'"
                :class="tab === 'sewa' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="rounded-full px-4 py-2 transition">{{ __('site.nav.sewa') }}</button>
            <button type="button" role="tab" :aria-selected="tab === 'baru'" @click="tab = 'baru'"
                :class="tab === 'baru' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="rounded-full px-4 py-2 transition">{{ __('site.nav.proyek') }}</button>
        </div>
        <form method="GET" :action="tab === 'baru' ? '{{ route('projects.index') }}' : '{{ route('properties.index') }}'" class="mt-3 grid gap-3 md:grid-cols-2 lg:grid-cols-4">
            <template x-if="tab !== 'baru'">
                <input type="hidden" name="status" :value="tab" />
            </template>
            <label class="block">
                <span class="text-xs font-semibold text-slate-500">Lokasi</span>
                <select name="city" class="kt-select mt-1 w-full" :disabled="tab === 'baru'">
                    <option value="">Semua Lokasi</option>
                    @foreach ($cities as $row)
                        @php $cityName = is_array($row) ? ($row['city'] ?? null) : ($row->city ?? null); @endphp
                        @if ($cityName)
                            <option value="{{ $cityName }}" @selected(request('city') === $cityName)>{{ $cityName }}</option>
                        @endif
                    @endforeach
                </select>
            </label>
            <label class="block" x-show="tab !== 'baru'">
                <span class="text-xs font-semibold text-slate-500">Tipe</span>
                <select name="type" class="kt-select mt-1 w-full">
                    <option value="">Semua Tipe</option>
                    @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block" x-show="tab !== 'baru'">
                <span class="text-xs font-semibold text-slate-500">Harga Min (Rp)</span>
                <input type="number" min="0" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="kt-input mt-1 w-full" />
            </label>
            <label class="block" x-show="tab !== 'baru'">
                <span class="text-xs font-semibold text-slate-500">Harga Max (Rp)</span>
                <input type="number" min="0" name="max_price" value="{{ request('max_price') }}" placeholder="Maks" class="kt-input mt-1 w-full" />
            </label>
            <div class="md:col-span-2 lg:col-span-4">
                <button type="submit" class="kt-btn kt-btn-primary w-full !bg-blue-600 hover:!bg-blue-700 !py-2.5 text-base font-bold"><i class="ki-filled ki-magnifier"></i> Cari</button>
            </div>
        </form>
    </div>
    {{-- Popular searches --}}
    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
        <span class="font-semibold text-slate-500">Populer:</span>
        @foreach ($popularSearches as $pop)
            <a href="{{ route('properties.index', $pop['params']) }}" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 font-medium text-slate-600 shadow-sm transition hover:border-blue-300 hover:text-blue-700">{{ $pop['label'] }}</a>
        @endforeach
    </div>
    {{-- Akses cepat --}}
    <div class="mt-4 grid grid-cols-4 gap-2 lg:grid-cols-8">
        @foreach ($quickLinks as $ql)
            <a href="{{ $ql['url'] }}" class="group flex flex-col items-center gap-1.5 rounded-xl border border-slate-100 bg-white px-2 py-3 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="grid size-10 place-items-center rounded-full bg-blue-600/10 text-lg text-blue-700 transition group-hover:bg-blue-600 group-hover:text-white"><i class="ki-filled {{ $ql['icon'] }}"></i></span>
                <span class="text-[11px] font-semibold leading-tight text-slate-700 group-hover:text-blue-700">{{ $ql['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- ============ 2. TRUST STRIP KOMPAK ============ --}}
@if ($show('trust'))
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 pt-6 lg:px-6">
        <div class="grid grid-cols-2 gap-2 lg:grid-cols-4">
            <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-blue-600"><i class="ki-filled ki-shield-tick"></i></span>
                <div class="text-xs"><div class="font-bold text-[#0F2A4A]">Listing Terkurasi</div><p class="text-slate-500">Diperiksa sebelum tayang</p></div>
            </div>
            <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-blue-600"><i class="ki-filled ki-document"></i></span>
                <div class="text-xs"><div class="font-bold text-[#0F2A4A]">Info Transparan</div><p class="text-slate-500">Harga &amp; spek apa adanya</p></div>
            </div>
            <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-blue-600"><i class="ki-filled ki-people"></i></span>
                <div class="text-xs"><div class="font-bold text-[#0F2A4A]">Langsung ke Agen</div><p class="text-slate-500">Chat &amp; survei cepat</p></div>
            </div>
            <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-blue-600"><i class="ki-filled ki-filter-search"></i></span>
                <div class="text-xs"><div class="font-bold text-[#0F2A4A]">Sesuai Kebutuhan</div><p class="text-slate-500">Filter kota, tipe &amp; bujet</p></div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ 3. PROPERTI PILIHAN ============ --}}
@if ($show('featured'))
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Pilihan Editor</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Properti Pilihan untuk Anda</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        @if ($featured->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                Belum ada properti unggulan. Data akan tampil setelah properti dipublikasikan.
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($featured as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 4. KATEGORI KOMPAK ============ --}}
@if ($show('categories'))
<section class="bg-[#F6F8FB]">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Kategori</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Cari Berdasarkan Tipe Hunian</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Semua →</a>
        </div>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-8">
            @foreach ($categoryTiles as $i => $cat)
                @php
                    $grad = $gradients[$i % count($gradients)];
                    $link = $cat['empty'] ? route('properties.index') : route('properties.index', ['type' => $cat['type']]);
                    $catImg = ! empty($cat['type']) && file_exists(public_path('landing/category-'.$cat['type'].'.jpg')) ? asset('landing/category-'.$cat['type'].'.jpg') : null;
                @endphp
                <a href="{{ $link }}" class="group relative flex min-h-36 flex-col justify-end overflow-hidden rounded-xl bg-gradient-to-br p-4 text-white shadow-sm transition hover:shadow-md sm:min-h-44 {{ $grad }}">
                    @if ($catImg)
                        <img src="{{ $catImg }}" alt="{{ $cat['name'] }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/85 via-[#0F2A4A]/25 to-transparent" aria-hidden="true"></div>
                    @endif
                    <span class="relative">
                        <span class="block truncate text-sm font-bold leading-tight">{{ $cat['name'] }}</span>
                        <span class="block text-[11px] text-white/85">{{ $cat['empty'] ? 'Segera hadir' : $cat['count'].' properti' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 5. PROYEK BARU (strip scroll-snap; tampil bila ada data) ============ --}}
@if ($homeProjects->isNotEmpty())
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Pengembang Terpercaya</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Proyek Baru</h2>
            </div>
            <a href="{{ route('projects.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Lihat Semua →</a>
        </div>
        <div class="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 lg:mx-0 lg:px-0">
            @foreach ($homeProjects as $i => $project)
                @php
                    $p = is_array($project) ? (object) $project : $project;
                    $devName = isset($p->developer) && is_object($p->developer) ? ($p->developer->name ?? null) : ($p->developer_name ?? null);
                    $grad = $gradients[$i % count($gradients)];
                    $pUrl = ! empty($p->slug) ? route('projects.show', $p->slug) : route('projects.index');
                    $pCover = ! empty($p->slug) && file_exists(public_path('landing/project-'.$p->slug.'.jpg')) ? asset('landing/project-'.$p->slug.'.jpg') : null;
                @endphp
                <a href="{{ $pUrl }}" class="w-72 shrink-0 snap-start overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition hover:shadow-md">
                    <div class="relative flex h-28 items-end overflow-hidden bg-gradient-to-br {{ $grad }} p-4 text-white">
                        @if ($pCover)
                            <img src="{{ $pCover }}" alt="{{ $p->name ?? 'Proyek' }}" loading="lazy" class="absolute inset-0 size-full object-cover" onerror="this.remove()" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/70 via-transparent to-transparent" aria-hidden="true"></div>
                        @else
                            <span class="grid size-11 place-items-center rounded-xl bg-white/20 text-xl font-extrabold">{{ strtoupper(mb_substr($p->name ?? 'P', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="truncate text-sm font-bold text-[#0F2A4A]">{{ $p->name ?? 'Proyek' }}</div>
                        <div class="mt-0.5 truncate text-xs text-slate-500">{{ $devName ? $devName.' · ' : '' }}{{ $p->city ?? '' }}</div>
                        <div class="mt-1 text-xs font-bold text-blue-700">
                            @if (! empty($p->price_from))
                                Mulai Rp {{ number_format((int) $p->price_from, 0, ',', '.') }}
                            @else
                                Lihat detail proyek
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 6. KOLEKSI KOMPAK ============ --}}
@if ($show('collections') && $collectionTiles->isNotEmpty())
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Koleksi</p>
            <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Jelajahi Hunian Istimewa</h2>
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ($collectionTiles as $i => $col)
                @php
                    $grad = $gradients[($i + 1) % count($gradients)];
                    $colSlug = \Illuminate\Support\Str::slug($col['label'] ?? '');
                    $colImgMap = ['rumah-mewah' => 'collection-rumah-mewah.jpg', 'villa-eksklusif' => 'collection-villa-eksklusif.jpg', 'modern-minimalis' => 'collection-modern-minimalis.jpg', 'tropical-living' => 'collection-tropical-living.jpg', 'apartemen-premium' => 'collection-apartemen-premium.jpg'];
                    $colImg = isset($colImgMap[$colSlug]) && file_exists(public_path('landing/'.$colImgMap[$colSlug])) ? asset('landing/'.$colImgMap[$colSlug]) : null;
                @endphp
                <a href="{{ route('properties.index', $col['params']) }}" class="group relative flex h-40 flex-col justify-end overflow-hidden rounded-xl bg-gradient-to-br p-4 text-white shadow-sm transition hover:shadow-md {{ $grad }}">
                    @if ($colImg)
                        <img src="{{ $colImg }}" alt="{{ $col['label'] }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/85 via-[#0F2A4A]/25 to-transparent" aria-hidden="true"></div>
                    @else
                        <div class="pointer-events-none absolute -right-6 -top-6 size-24 rounded-full border-[12px] border-white/15" aria-hidden="true"></div>
                    @endif
                    <div class="relative">
                        <div class="text-base font-extrabold leading-tight">{{ $col['label'] }}</div>
                        <p class="mt-0.5 line-clamp-1 text-[11px] text-white/80">{{ $col['desc'] }}</p>
                        <div class="mt-1 text-xs font-semibold text-white/90">{{ $col['count'] }} properti <span aria-hidden="true">→</span></div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 7. KOTA KOMPAK ============ --}}
@if ($show('cities'))
<section class="bg-[#F6F8FB]">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Lokasi</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Kota Populer</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Semua Kota →</a>
        </div>
        @if ($cityRows->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                Data kota akan tampil setelah ada properti yang dipublikasikan.
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ($cityRows as $i => $row)
                    @php
                        $cityName = is_array($row) ? ($row['city'] ?? '') : ($row->city ?? '');
                        $total = is_array($row) ? ($row['total'] ?? null) : ($row->total ?? null);
                        $grad = $gradients[$i % count($gradients)];
                        $citySlug = \Illuminate\Support\Str::slug((string) $cityName);
                        $cityImg = $citySlug && file_exists(public_path('landing/city-'.$citySlug.'.jpg')) ? asset('landing/city-'.$citySlug.'.jpg') : null;
                    @endphp
                    <a href="{{ route('properties.index', ['city' => $cityName]) }}" class="group relative flex items-center justify-between gap-2 overflow-hidden rounded-xl bg-gradient-to-br p-4 text-white shadow-sm transition hover:shadow-md {{ $grad }}">
                        @if ($cityImg)
                            <img src="{{ $cityImg }}" alt="Properti di {{ $cityName }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                            <div class="absolute inset-0 bg-gradient-to-r from-[#0F2A4A]/80 via-[#0F2A4A]/45 to-[#0F2A4A]/15" aria-hidden="true"></div>
                        @endif
                        <span class="relative flex items-center gap-2.5 min-w-0">
                            @php $logoSlug = \Illuminate\Support\Str::slug($cityName); @endphp
                            @if ($logoSlug && file_exists(public_path('landing/logos/city-'.$logoSlug.'.png')))
                                <img src="{{ asset('landing/logos/city-'.$logoSlug.'.png') }}" alt="Lambang {{ $cityName }}" loading="lazy" class="size-10 shrink-0 rounded-full bg-white p-1 object-contain shadow" onerror="this.remove()" />
                            @else
                                <span aria-hidden="true" class="grid size-10 shrink-0 place-items-center rounded-full bg-white/20 text-sm font-extrabold">{{ mb_substr((string) $cityName, 0, 1) }}</span>
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold sm:text-base">{{ $cityName }}</span>
                                <span class="block text-[11px] text-white/85">{{ $total ? $total.' properti' : 'Lihat properti' }}</span>
                            </span>
                        </span>
                        <span aria-hidden="true" class="relative grid size-8 shrink-0 place-items-center rounded-full bg-white/20 transition group-hover:bg-white/30">→</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 8. PROPERTI TERBARU ============ --}}
@if ($show('latest'))
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Baru Tayang</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Properti Terbaru</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Lihat Katalog →</a>
        </div>
        @if ($latest->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                Belum ada properti terbaru. Jelajahi <a href="{{ route('properties.index') }}" class="font-semibold text-blue-600 hover:underline">seluruh katalog</a> untuk pilihan lainnya.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latest as $property)
                    <div>
                        <x-property-card :property="$property" />
                        <p class="mt-1.5 text-xs text-slate-400">Dipublikasikan {{ optional($property->published_at ?? $property->created_at)->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 9. AGEN KOMPAK ============ --}}
@if ($show('agents') && $agents->isNotEmpty())
<section class="bg-[#F6F8FB]">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Agen Terverifikasi</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Dibantu Agen Profesional</h2>
            </div>
            <a href="{{ route('agents.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Telusuri Agen →</a>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($agents as $agent)
                @php
                    $a = is_array($agent) ? (object) $agent : $agent;
                    $profile = is_array($a->agentProfile ?? null) ? (object) $a->agentProfile : ($a->agentProfile ?? null);
                    $agentName = $a->name ?? 'Agen';
                    $agency = (is_object($profile) ? ($profile->agency ?? null) : null) ?? $a->agency ?? 'Agen Independen';
                    $photo = (is_object($profile) ? ($profile->photo ?? null) : null) ?? $a->photo ?? null;
                    $wa = (is_object($profile) ? ($profile->wa ?? null) : null) ?? $a->wa ?? null;
                    $listingCount = $a->published_properties_count ?? $a->properties_count ?? null;
                    $profileId = (is_object($profile) ? ($profile->id ?? null) : null) ?? null;
                    $profileUrl = $profileId ? route('agents.show', $profileId) : route('agents.index');
                    $waUrl = $wa ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $wa) : null;
                @endphp
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm transition hover:shadow-md">
                    @if ($photo && file_exists(public_path('storage/'.$photo)))
                        <img src="{{ asset('storage/'.$photo) }}" alt="{{ $agentName }}" loading="lazy" class="size-12 shrink-0 rounded-full object-cover ring-2 ring-blue-100" />
                    @else
                        <span class="grid size-12 shrink-0 place-items-center rounded-full bg-blue-600/10 text-lg font-extrabold text-blue-700">{{ strtoupper(mb_substr($agentName, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-bold text-[#0F2A4A]">{{ $agentName }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $agency }}</div>
                        <div class="text-[11px] font-semibold text-blue-600">{{ $listingCount !== null ? $listingCount.' listing aktif' : 'Agen terverifikasi' }}</div>
                    </div>
                    <a href="{{ $waUrl ?? $profileUrl }}" @if ($waUrl) target="_blank" rel="noopener" @endif class="kt-btn kt-btn-primary shrink-0 !px-3 !py-1.5 text-xs font-semibold">Hubungi</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 10. SELLER CTA + KPR ============ --}}
@if ($show('seller'))
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="relative overflow-hidden rounded-2xl bg-[#0F2A4A] p-6 text-white sm:p-8">
                @if (file_exists(public_path('landing/seller-cta.jpg')))
                    <img src="{{ asset('landing/seller-cta.jpg') }}" alt="Pasarkan properti Anda" loading="lazy" class="absolute inset-0 size-full object-cover opacity-40" onerror="this.remove()" />
                    <div class="absolute inset-0 bg-gradient-to-r from-[#0F2A4A] via-[#0F2A4A]/85 to-[#0F2A4A]/30" aria-hidden="true"></div>
                @else
                    <div class="pointer-events-none absolute -right-10 -top-10 size-40 rounded-full border-[20px] border-white/10" aria-hidden="true"></div>
                @endif
                <div class="relative">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-300">Untuk Pemilik &amp; Agen</p>
                <h2 class="mt-2 text-xl font-extrabold tracking-tight sm:text-2xl">Punya Properti? Ubah Jadi Cuan Lebih Cepat.</h2>
                <p class="mt-2 text-sm text-white/80">Pasang listing dalam hitungan menit dan pantau peminat yang masuk.</p>
                <ul class="mt-4 space-y-1.5 text-sm text-white/90">
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Gratis pasang, tayang seketika</li>
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Leads langsung ke WhatsApp Anda</li>
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Dasbor performa listing</li>
                </ul>
                <div class="mt-5 flex flex-wrap gap-2">
                    @auth
                        <a href="{{ route('agent.listings.create') }}" class="kt-btn bg-white font-semibold text-[#0F2A4A] hover:bg-white/90">Pasang Properti →</a>
                    @else
                        <a href="{{ route('register') }}" class="kt-btn bg-white font-semibold text-[#0F2A4A] hover:bg-white/90">Pasang Properti →</a>
                    @endauth
                    <a href="{{ route('contact') }}" class="kt-btn border border-white/30 font-semibold text-white hover:bg-white/10">Hubungi Kami</a>
                </div>
                </div>
            </div>
            <div id="kpr" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-[#F6F8FB] p-6 sm:p-8" x-data="{ price: 800000000, dp: 20, tenor: 20, rate: 6.5,
                    fmt(v) { return 'Rp ' + Math.round(v).toLocaleString('id-ID'); },
                    get loan() { return this.price * (1 - this.dp / 100); },
                    get n() { return Math.max(1, this.tenor * 12); },
                    get r() { return this.rate / 12 / 100; },
                    get pay() { if (this.r <= 0) return this.loan / this.n; const p = Math.pow(1 + this.r, this.n); return this.loan * this.r * p / (p - 1); } }">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Simulasi KPR</p>
                <h2 class="mt-1 text-xl font-extrabold tracking-tight text-[#0F2A4A]">Hitung Estimasi Cicilan</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Harga Properti (Rp)</span>
                        <input type="number" min="0" x-model.number="price" class="kt-input mt-1 w-full" />
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Uang Muka / DP (%)</span>
                        <input type="number" min="0" max="100" x-model.number="dp" class="kt-input mt-1 w-full" />
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Tenor (tahun)</span>
                        <input type="number" min="1" max="30" x-model.number="tenor" class="kt-input mt-1 w-full" />
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Bunga (%/tahun)</span>
                        <input type="number" min="0" step="0.1" x-model.number="rate" class="kt-input mt-1 w-full" />
                    </label>
                </div>
                <div class="mt-4 rounded-xl bg-white p-4 shadow-sm">
                    <div class="text-xs text-slate-500">Estimasi cicilan per bulan</div>
                    <div class="text-2xl font-extrabold text-blue-700" x-text="fmt(pay)"></div>
                    <div class="mt-1 text-xs text-slate-500">Pinjaman <span x-text="fmt(loan)"></span> · <span x-text="tenor"></span> tahun</div>
                </div>
                <a href="{{ route('contact') }}" class="kt-btn kt-btn-primary mt-3 w-full font-semibold">Ajukan KPR →</a>
                <p class="mt-2 text-[11px] text-slate-400">Hasil ini hanya estimasi, bukan penawaran resmi bank.</p>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ 11. ARTIKEL KOMPAK ============ --}}
@if ($show('articles') && $articles->isNotEmpty())
<section class="bg-[#F6F8FB]">
    <div class="mx-auto max-w-7xl px-4 py-8 lg:px-6">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Edukasi</p>
                <h2 class="text-xl font-extrabold tracking-tight text-[#0F2A4A] sm:text-2xl">Wawasan Properti</h2>
            </div>
            <a href="{{ route('articles.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Semua Artikel →</a>
        </div>
        <div class="grid gap-3 lg:grid-cols-2">
            @foreach ($articles->take(6) as $article)
                @php $art = is_array($article) ? (object) $article : $article; @endphp
                <a href="{{ route('articles.show', $art->slug) }}" class="group flex items-center gap-4 rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition hover:shadow-md">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-blue-600/10 text-lg font-extrabold text-blue-700">{{ strtoupper(mb_substr($art->title ?? 'R', 0, 1)) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-bold text-[#0F2A4A] group-hover:text-blue-700">{{ $art->title }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($art->body ?? ''), 90) }}</span>
                        <span class="mt-0.5 block text-[11px] text-slate-400">@if (! empty($art->city)){{ $art->city }} · @endif{{ optional($art->created_at)->format('d M Y') }}</span>
                    </span>
                    <span aria-hidden="true" class="shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-blue-600">→</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
