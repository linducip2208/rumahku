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
    $heroTitle = $landingGet('hero_title', 'Temukan Rumah Impianmu dengan Mudah');
    $heroSubtitle = $landingGet('hero_subtitle', 'Jelajahi properti dijual & disewa di seluruh Indonesia — rumah, apartemen, ruko, tanah, dan villa pilihan.');
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

    // Foto editorial landing (AI image gagal total — API key invalid).
    // Pakai Picsum seed deterministik (foto real), gradient tetap sebagai background.
    $pix = fn (string $seed, int $w = 1200, int $h = 800): string => "https://picsum.photos/seed/{$seed}/{$w}/{$h}";

    $toCol = fn (mixed $v): \Illuminate\Support\Collection => $v instanceof \Illuminate\Support\Collection ? $v : collect($v ?? []);
    $featured = $toCol($featured ?? null);
    $latest = $toCol($latest ?? null);
    $cities = $toCol($cities ?? null);
    $agents = $toCol($agents ?? null);
    $articles = $toCol($articles ?? null);

    $knownProps = $featured->concat($latest)->unique('id');

    // ---- Kategori (8) ----
    $catImageByType = ['rumah' => 'category-house.jpg', 'apartemen' => 'category-apartment.jpg', 'tanah' => 'category-land.jpg', 'villa' => 'category-villa.jpg', 'ruko' => 'category-ruko.jpg', 'kost' => 'category-kost.jpg', 'gedung' => 'category-gedung.jpg', 'gudang' => 'category-gudang.jpg'];
    $defaultCategories = [
        ['name' => 'Rumah', 'type' => 'rumah', 'image' => 'category-house.jpg'],
        ['name' => 'Apartemen', 'type' => 'apartemen', 'image' => 'category-apartment.jpg'],
        ['name' => 'Tanah', 'type' => 'tanah', 'image' => 'category-land.jpg'],
        ['name' => 'Villa', 'type' => 'villa', 'image' => 'category-villa.jpg'],
        ['name' => 'Ruko', 'type' => 'ruko', 'image' => 'category-ruko.jpg'],
        ['name' => 'Kost', 'type' => 'kost', 'image' => 'category-kost.jpg'],
        ['name' => 'Gedung', 'type' => 'gedung', 'image' => 'category-gedung.jpg'],
        ['name' => 'Gudang', 'type' => 'gudang', 'image' => 'category-gudang.jpg'],
    ];
    $rawCategories = $toCol($categories ?? null);
    if ($rawCategories->isNotEmpty()) {
        $categoryTiles = $rawCategories->map(function ($c) use ($catImageByType) {
            $c = is_array($c) ? (object) $c : $c;
            $type = $c->type ?? null;
            return [
                'name' => $c->name ?? $c->label ?? ucfirst((string) $type),
                'type' => $type,
                'count' => $c->count ?? null,
                'empty' => (bool) ($c->empty ?? (($c->count ?? 0) === 0)),
                'image' => $c->image ?? ($catImageByType[$type] ?? null),
            ];
        });
    } else {
        $categoryTiles = collect($defaultCategories)->map(function ($c) use ($knownProps) {
            $count = $knownProps->where('type', $c['type'])->count();
            return array_merge($c, ['count' => $count, 'empty' => $count === 0]);
        });
    }

    // ---- Koleksi editorial (5) ----
    $defaultCollectionImages = ['collection-luxury.jpg', 'collection-tropical.jpg', 'collection-minimalist.jpg', null, null];
    $defaultCollections = [
        ['key' => 'luxury', 'label' => 'Hunian Mewah', 'desc' => 'Eksklusif & prestisius', 'image' => 'collection-luxury.jpg', 'params' => ['status' => 'jual', 'min_price' => 2000000000]],
        ['key' => 'tropical', 'label' => 'Villa Tropis', 'desc' => 'Resor pribadi impian', 'image' => 'collection-tropical.jpg', 'params' => ['type' => 'villa']],
        ['key' => 'minimalist', 'label' => 'Minimalis Modern', 'desc' => 'Desain kekinian', 'image' => 'collection-minimalist.jpg', 'params' => ['type' => 'rumah', 'status' => 'jual']],
        ['key' => 'family', 'label' => 'Siap Huni Keluarga', 'desc' => 'Langsung tempati', 'image' => null, 'params' => ['type' => 'rumah']],
        ['key' => 'rent', 'label' => 'Sewa Praktis', 'desc' => 'Fleksibel & cepat', 'image' => null, 'params' => ['status' => 'sewa']],
    ];
    $rawCollections = $toCol($collections ?? null);
    if ($rawCollections->isNotEmpty()) {
        $collectionTiles = $rawCollections->values()->map(function ($c, $idx) use ($defaultCollectionImages) {
            $c = is_array($c) ? (object) $c : $c;
            $params = is_array($c->query ?? null) ? $c->query : (is_array($c->params ?? null) ? $c->params : []);
            if ($params === []) {
                $params = array_filter([
                    'type' => $c->type ?? null,
                    'status' => $c->status ?? null,
                    'min_price' => $c->min_price ?? null,
                    'max_price' => $c->max_price ?? null,
                ], fn ($v) => $v !== null && $v !== '');
            }
            $label = $c->label ?? $c->name ?? '';
            return [
                'key' => $c->key ?? \Illuminate\Support\Str::slug((string) $label),
                'label' => $label,
                'desc' => $c->desc ?? '',
                'image' => $c->image ?? ($defaultCollectionImages[$idx] ?? null),
                'count' => $c->count ?? null,
                'params' => $params,
            ];
        });
    } else {
        $collectionTiles = collect($defaultCollections)->map(fn ($c) => array_merge($c, ['count' => null]));
    }

    $cityRows = $cities instanceof \Illuminate\Support\Collection ? $cities->take(5) : collect();
    $gradients = [
        'from-blue-700 via-blue-600 to-cyan-500',
        'from-[#0F2A4A] via-[#1B3A5F] to-blue-600',
        'from-emerald-700 via-emerald-600 to-teal-500',
        'from-amber-600 via-orange-500 to-rose-500',
        'from-violet-700 via-indigo-600 to-blue-500',
        'from-rose-700 via-rose-600 to-pink-500',
    ];
@endphp

@section('content')
{{-- ============ 1. HERO ============ --}}
@if ($show('hero'))
<section class="relative overflow-hidden bg-[#0F2A4A] text-white">
    @if ($heroFile)
        <img src="{{ asset($heroFile) }}" alt="Properti pilihan" class="absolute inset-0 size-full object-cover" fetchpriority="high" onerror="this.remove()" />
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#0F2A4A] via-[#1B3A5F] to-blue-700" aria-hidden="true"></div>
        <img src="{{ $pix('rumahku-hero', 1920, 900) }}" alt="" aria-hidden="true" loading="eager" fetchpriority="high" class="absolute inset-0 size-full object-cover" onerror="this.remove()" />
        <div class="absolute -top-24 -right-24 size-72 rounded-full border-[24px] border-white/10" aria-hidden="true"></div>
        <div class="absolute -bottom-28 -left-16 size-80 rounded-full border-[32px] border-white/10" aria-hidden="true"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-[#0F2A4A]/80 via-[#0F2A4A]/55 to-[#0F2A4A]/85" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 lg:px-6 pt-14 pb-36 md:pt-20 md:pb-44">
        <p class="inline-flex items-center gap-2 text-xs font-semibold bg-white/15 border border-white/25 rounded-full px-3 py-1.5 backdrop-blur">
            <i class="ki-filled ki-home"></i> Portal Properti Indonesia
        </p>
        <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight max-w-2xl">{{ $heroTitle }}</h1>
        <p class="mt-3 text-white/85 max-w-xl text-sm sm:text-base">{{ $heroSubtitle }}</p>

        {{-- Panel pencarian --}}
        <div x-data="{ tab: 'jual' }" class="mt-8 max-w-4xl bg-white text-foreground rounded-2xl shadow-2xl p-4 sm:p-5">
            <div class="flex gap-2 text-sm font-semibold" role="tablist" aria-label="Status listing">
                <button type="button" role="tab" :aria-selected="tab === 'jual'" @click="tab = 'jual'"
                    :class="tab === 'jual' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 transition">Jual</button>
                <button type="button" role="tab" :aria-selected="tab === 'sewa'" @click="tab = 'sewa'"
                    :class="tab === 'sewa' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 transition">Sewa</button>
                <button type="button" role="tab" :aria-selected="tab === 'baru'" @click="tab = 'baru'"
                    :class="tab === 'baru' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 transition">Properti Baru</button>
            </div>
            <form method="GET" action="{{ route('properties.index') }}" class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-[1fr_150px_130px_130px]">
                <input type="hidden" name="status" :value="tab === 'sewa' ? 'sewa' : (tab === 'jual' ? 'jual' : '')" />
                <label class="block md:col-span-2 lg:col-span-1">
                    <span class="text-xs font-semibold text-slate-500">Lokasi</span>
                    <select name="city" class="kt-select mt-1 w-full">
                        <option value="">Semua Lokasi</option>
                        @foreach ($cities as $row)
                            @php $cityName = is_array($row) ? ($row['city'] ?? null) : ($row->city ?? null); @endphp
                            @if ($cityName)
                                <option value="{{ $cityName }}" @selected(request('city') === $cityName)>{{ $cityName }}</option>
                            @endif
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-500">Tipe</span>
                    <select name="type" class="kt-select mt-1 w-full">
                        <option value="">Semua Tipe</option>
                        @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                            <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-500">Harga Min (Rp)</span>
                    <input type="number" min="0" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="kt-input mt-1 w-full" />
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-500">Harga Max (Rp)</span>
                    <input type="number" min="0" name="max_price" value="{{ request('max_price') }}" placeholder="Maks" class="kt-input mt-1 w-full" />
                </label>
                <div class="flex flex-col sm:flex-row gap-2 md:col-span-2 lg:col-span-4">
                    <button type="submit" class="kt-btn kt-btn-primary flex-1 !py-2.5 font-semibold"><i class="ki-filled ki-magnifier"></i> Cari Properti</button>
                    <a href="{{ route('register') }}" class="kt-btn kt-btn-outline flex-1 !py-2.5 font-semibold text-center">Pasarkan Properti Anda</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endif

{{-- ============ 2. TRUST STRIP ============ --}}
@if ($show('trust'))
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-6">
        <div class="grid gap-3 grid-cols-2 lg:grid-cols-4">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <span class="grid place-items-center size-10 rounded-xl bg-blue-50 text-blue-600 shrink-0"><i class="ki-filled ki-shield-tick text-lg"></i></span>
                <div class="text-sm"><div class="font-bold text-[#0F2A4A]">Listing Terkurasi</div><p class="text-xs text-slate-500">Diperiksa sebelum tayang</p></div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <span class="grid place-items-center size-10 rounded-xl bg-blue-50 text-blue-600 shrink-0"><i class="ki-filled ki-document text-lg"></i></span>
                <div class="text-sm"><div class="font-bold text-[#0F2A4A]">Info Jelas & Transparan</div><p class="text-xs text-slate-500">Harga & spek apa adanya</p></div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <span class="grid place-items-center size-10 rounded-xl bg-blue-50 text-blue-600 shrink-0"><i class="ki-filled ki-people text-lg"></i></span>
                <div class="text-sm"><div class="font-bold text-[#0F2A4A]">Langsung ke Agen</div><p class="text-xs text-slate-500">Chat & survei cepat</p></div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <span class="grid place-items-center size-10 rounded-xl bg-blue-50 text-blue-600 shrink-0"><i class="ki-filled ki-filter-search text-lg"></i></span>
                <div class="text-sm"><div class="font-bold text-[#0F2A4A]">Sesuai Kebutuhan</div><p class="text-xs text-slate-500">Filter kota, tipe & bujet</p></div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ 3. PROPERTI PILIHAN ============ --}}
@if ($show('featured'))
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="flex items-end justify-between gap-3 mb-2">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Pilihan Editor</p>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Properti Pilihan untuk Anda</h2>
                <p class="text-sm text-slate-500 mt-1">Kurasi listing terbaik minggu ini di kota-kota favorit.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Lihat Semua Properti →</a>
        </div>
        @if ($featured->isEmpty())
            <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-slate-500">
                Belum ada properti unggulan. Data akan tampil setelah properti dipublikasikan.
            </div>
        @else
            <div class="mt-5 grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featured as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 4. KATEGORI ============ --}}
@if ($show('categories'))
<section class="bg-[#FAF7F2]">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Kategori</p>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Cari Berdasarkan Tipe Hunian</h2>
            <p class="text-sm text-slate-500 mt-1">Delapan tipe properti, dari rumah tapak hingga gudang industri.</p>
        </div>
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            @foreach ($categoryTiles as $i => $cat)
                @php
                    $img = $cat['image'] && file_exists(public_path('landing/'.$cat['image'])) ? asset('landing/'.$cat['image']) : $pix('rumahku-cat-'.($cat['type'] ?? $i), 800, 600);
                    $grad = $gradients[$i % count($gradients)];
                    $link = $cat['empty'] ? route('properties.index') : route('properties.index', array_filter(['type' => in_array($cat['type'], ['rumah','apartemen','tanah','ruko','villa'], true) ? $cat['type'] : null]));
                @endphp
                <a href="{{ $link }}" class="group relative overflow-hidden rounded-2xl min-h-40 sm:min-h-48 flex flex-col justify-end p-4 text-white shadow-sm hover:shadow-lg transition bg-gradient-to-br {{ $grad }}">
                    @if ($img)
                        <img src="{{ $img }}" alt="{{ $cat['name'] }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/85 via-[#0F2A4A]/25 to-transparent" aria-hidden="true"></div>
                    @endif
                    <div class="relative">
                        <div class="font-bold leading-tight">{{ $cat['name'] }}</div>
                        <div class="mt-1 text-xs text-white/85">
                            @if ($cat['empty'])
                                <span class="inline-flex items-center rounded-full bg-white/20 border border-white/30 px-2 py-0.5 font-semibold">Segera hadir</span>
                            @else
                                {{ $cat['count'] }} properti →
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 5. KOLEKSI ============ --}}
@if ($show('collections'))
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Koleksi</p>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Jelajahi Hunian Istimewa</h2>
            <p class="text-sm text-slate-500 mt-1">Gaya hidup yang berbeda, dalam koleksi yang dikurasi khusus.</p>
        </div>
        <div class="grid gap-4 lg:grid-cols-4">
            @foreach ($collectionTiles as $i => $col)
                @php
                    $img = ($col['image'] ?? null) && file_exists(public_path('landing/'.$col['image'])) ? asset('landing/'.$col['image']) : $pix('rumahku-col-'.$i, 1200, 800);
                    $grad = $gradients[($i + 1) % count($gradients)];
                    $isWide = $i === 0;
                    $clickable = ($col['count'] ?? null) === null || ($col['count'] ?? 0) > 0;
                    $url = $clickable ? route('properties.index', $col['params']) : null;
                @endphp
                @if ($url)
                    <a href="{{ $url }}" class="group relative overflow-hidden rounded-2xl flex flex-col justify-end p-5 text-white shadow-sm hover:shadow-lg transition bg-gradient-to-br {{ $grad }} {{ $isWide ? 'lg:col-span-2 min-h-64 lg:min-h-80' : 'min-h-56 lg:min-h-64' }}">
                @else
                    <div class="relative overflow-hidden rounded-2xl flex flex-col justify-end p-5 text-white shadow-sm bg-gradient-to-br {{ $grad }} {{ $isWide ? 'lg:col-span-2 min-h-64 lg:min-h-80' : 'min-h-56 lg:min-h-64' }}">
                @endif
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $col['label'] }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/85 via-[#0F2A4A]/25 to-transparent" aria-hidden="true"></div>
                        @endif
                        <div class="relative">
                            <div class="font-extrabold {{ $isWide ? 'text-xl lg:text-2xl' : 'text-lg' }} leading-tight">{{ $col['label'] }}</div>
                            @if (! empty($col['desc']))
                                <p class="text-xs text-white/80 mt-1">{{ $col['desc'] }}</p>
                            @endif
                            <div class="mt-2 text-xs font-semibold text-white/90">
                                @if (isset($col['count']) && $col['count'] !== null)
                                    {{ $col['count'] }} properti
                                @endif
                                @if ($url) <span aria-hidden="true"> →</span> @endif
                            </div>
                        </div>
                @if ($url)
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 6. KOTA ============ --}}
@if ($show('cities'))
<section class="bg-[#FAF7F2]">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="flex items-end justify-between gap-3 mb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Lokasi</p>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Kota Populer</h2>
                <p class="text-sm text-slate-500 mt-1">Mulai pencarian dari kota dengan listing terbanyak.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Semua Kota →</a>
        </div>
        @if ($cityRows->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-slate-500">
                Data kota akan tampil setelah ada properti yang dipublikasikan.
            </div>
        @else
            <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                @foreach ($cityRows as $i => $row)
                    @php
                        $cityName = is_array($row) ? ($row['city'] ?? '') : ($row->city ?? '');
                        $total = is_array($row) ? ($row['total'] ?? null) : ($row->total ?? null);
                        $slug = \Illuminate\Support\Str::slug($cityName);
                        $img = $slug && file_exists(public_path('landing/city-'.$slug.'.jpg')) ? asset('landing/city-'.$slug.'.jpg') : $pix('rumahku-city-'.$slug, 800, 600);
                        $grad = $gradients[$i % count($gradients)];
                    @endphp
                    <a href="{{ route('properties.index', ['city' => $cityName]) }}" class="group relative overflow-hidden rounded-2xl flex flex-col justify-end p-4 sm:p-5 text-white shadow-sm hover:shadow-lg transition bg-gradient-to-br {{ $grad }} {{ $i === 0 ? 'col-span-2 row-span-2 min-h-64 lg:min-h-96' : 'min-h-36 sm:min-h-44' }}">
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $cityName }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105" onerror="this.remove()" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F2A4A]/85 via-[#0F2A4A]/25 to-transparent" aria-hidden="true"></div>
                        @else
                            <div class="pointer-events-none absolute -top-8 -right-8 size-28 rounded-full border-[14px] border-white/15" aria-hidden="true"></div>
                        @endif
                        <div class="relative">
                            <div class="font-bold leading-tight {{ $i === 0 ? 'text-xl lg:text-2xl' : '' }}">{{ $cityName }}</div>
                            <div class="mt-1 flex items-center justify-between text-xs text-white/85">
                                <span>{{ $total ? $total.' properti' : 'Lihat properti' }}</span>
                                <span aria-hidden="true">→</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 7. PROPERTI TERBARU ============ --}}
@if ($show('latest'))
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="flex items-end justify-between gap-3 mb-2">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Baru Tayang</p>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Properti Terbaru</h2>
                <p class="text-sm text-slate-500 mt-1">Listing paling segar yang baru dipublikasikan.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Lihat Katalog →</a>
        </div>
        @if ($latest->isEmpty())
            <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-slate-500">
                Belum ada properti terbaru. Jelajahi <a href="{{ route('properties.index') }}" class="font-semibold text-blue-600 hover:underline">seluruh katalog</a> untuk pilihan lainnya.
            </div>
        @else
            <div class="mt-5 grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latest as $property)
                    <div>
                        <x-property-card :property="$property" />
                        <p class="mt-2 text-xs text-slate-400">Dipublikasikan {{ optional($property->published_at ?? $property->created_at)->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ 8. AGEN ============ --}}
@if ($show('agents') && $agents->isNotEmpty())
<section class="bg-[#FAF7F2]">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="flex items-end justify-between gap-3 mb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Agen Terverifikasi</p>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Dibantu Agen Profesional</h2>
                <p class="text-sm text-slate-500 mt-1">Konsultasi gratis, survei terjadwal, negosiasi dibantu.</p>
            </div>
            <a href="{{ route('agents.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Telusuri Agen →</a>
        </div>
        <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($agents as $agent)
                @php
                    $a = is_array($agent) ? (object) $agent : $agent;
                    $profile = is_array($a->agentProfile ?? null) ? (object) $a->agentProfile : ($a->agentProfile ?? $a->profile ?? null);
                    $prof = is_array($profile) ? (object) $profile : $profile;
                    $agentName = $a->name ?? optional($a->user)->name ?? 'Agen';
                    $agency = (is_object($prof) ? ($prof->agency ?? null) : null) ?? $a->agency ?? 'Agen Independen';
                    $photo = (is_object($prof) ? ($prof->photo ?? null) : null) ?? $a->photo ?? null;
                    $wa = (is_object($prof) ? ($prof->wa ?? null) : null) ?? $a->wa ?? null;
                    $listingCount = $a->published_properties_count ?? $a->properties_count ?? $a->listings_count ?? null;
                    $profileId = (is_object($prof) ? ($prof->id ?? null) : null) ?? $a->profile_id ?? null;
                    $profileUrl = $profileId ? route('agents.show', $profileId) : route('agents.index');
                    $waUrl = $wa ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', (string) $wa) : null;
                @endphp
                <div class="rounded-2xl border border-slate-100 bg-white p-5 text-center shadow-sm hover:shadow-md transition">
                    @if ($photo && file_exists(public_path('storage/'.$photo)))
                        <img src="{{ asset('storage/'.$photo) }}" alt="{{ $agentName }}" loading="lazy" class="mx-auto size-20 rounded-full object-cover ring-2 ring-blue-100" />
                    @else
                        <span class="mx-auto grid place-items-center size-20 rounded-full bg-blue-600/10 text-blue-700 font-extrabold text-2xl">{{ strtoupper(substr($agentName, 0, 1)) }}</span>
                    @endif
                    <div class="mt-3 font-bold text-[#0F2A4A] truncate">{{ $agentName }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ $agency }}</div>
                    <div class="mt-1 text-xs font-semibold text-blue-600">{{ $listingCount !== null ? $listingCount.' listing aktif' : 'Agen terverifikasi' }}</div>
                    <a href="{{ $waUrl ?? $profileUrl }}" @if ($waUrl) target="_blank" rel="noopener" @endif class="mt-3 inline-flex w-full justify-center kt-btn kt-btn-primary !py-2 text-sm font-semibold">Hubungi</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ 9. SELLER CTA ============ --}}
@if ($show('seller'))
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        @php $sellerImg = file_exists(public_path('landing/seller-cta.jpg')) ? asset('landing/seller-cta.jpg') : $pix('rumahku-seller', 1000, 800); @endphp
        <div class="relative overflow-hidden rounded-3xl bg-[#0F2A4A] text-white grid gap-0 lg:grid-cols-2">
            @if ($sellerImg)
                <div class="relative min-h-64 lg:min-h-full">
                    <img src="{{ $sellerImg }}" alt="Pasarkan properti Anda" loading="lazy" class="absolute inset-0 size-full object-cover" onerror="this.closest('div.relative').remove()" />
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#0F2A4A]/40" aria-hidden="true"></div>
                </div>
            @endif
            <div class="p-6 sm:p-8 lg:p-12 {{ $sellerImg ? '' : 'lg:col-span-2' }}">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-300">Untuk Pemilik & Agen</p>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Punya Properti? Ubah Jadi Cuan Lebih Cepat.</h2>
                <p class="mt-2 text-white/80 text-sm sm:text-base">Pasang listing dalam hitungan menit, pantau leads real-time, dan closing dengan bantuan agen terverifikasi.</p>
                <ul class="mt-4 space-y-2 text-sm text-white/90">
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Gratis pasang, tayang seketika</li>
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Leads langsung ke WhatsApp Anda</li>
                    <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle text-emerald-300"></i> Dasbor performa & pengelola listing</li>
                </ul>
                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('register') }}" class="kt-btn bg-white text-[#0F2A4A] hover:bg-white/90 font-semibold !px-5 !py-2.5">Pasang Properti →</a>
                    <a href="#cta-lead" class="kt-btn border border-white/30 text-white hover:bg-white/10 font-semibold !px-5 !py-2.5">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ 10. ARTIKEL ============ --}}
@if ($articles->isNotEmpty())
<section class="bg-[#FAF7F2]">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-14">
        <div class="flex items-end justify-between gap-3 mb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">Edukasi</p>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F2A4A]">Wawasan Properti</h2>
                <p class="text-sm text-slate-500 mt-1">Panduan membeli, menjual, dan berinvestasi properti.</p>
            </div>
            <a href="{{ route('articles.index') }}" class="shrink-0 text-sm font-semibold text-blue-600 hover:underline">Semua Artikel →</a>
        </div>
        <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($articles->take(6) as $i => $article)
                @php
                    $a = is_array($article) ? (object) $article : $article;
                    $grad = $gradients[$i % count($gradients)];
                @endphp
                <a href="{{ route('articles.show', $a->slug) }}" class="group rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-sm hover:shadow-md transition">
                    <div class="relative h-36 bg-gradient-to-br {{ $grad }} flex items-center justify-center">
                        <span class="grid place-items-center size-14 rounded-2xl bg-white/20 border border-white/30 text-white font-extrabold text-2xl">{{ strtoupper(substr($a->title ?? 'R', 0, 1)) }}</span>
                        @if (! empty($a->city))
                            <span class="absolute top-3 left-3 text-[11px] font-bold uppercase tracking-wide bg-white/90 text-[#0F2A4A] rounded-full px-2.5 py-1">{{ $a->city }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="font-bold text-[#0F2A4A] leading-snug line-clamp-2 group-hover:text-blue-700">{{ $a->title }}</div>
                        <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($a->body ?? ''), 120) }}</p>
                        <div class="mt-3 text-xs text-slate-400">{{ optional($a->created_at)->format('d M Y') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
