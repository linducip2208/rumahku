@extends('layouts.metronic')

@section('title', 'Rumah123 — Jual, Sewa & Proyek Baru')

@push('styles')
    <x-seo-meta
        title="Rumah123 — Jual, Sewa & Proyek Baru"
        description="Rumah123 — Portal properti Indonesia: cari rumah, apartemen, tanah, ruko, villa dijual & disewa, proyek baru, agen, dan simulasi KPR."
        :canonical="route('home')"
    />
@endpush

@section('content')
<!-- Hero premium -->
<section class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-600 text-white">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -top-24 -right-24 size-72 rounded-full border-[24px] border-white/10"></div>
        <div class="absolute -bottom-28 -left-16 size-80 rounded-full border-[32px] border-white/10"></div>
        <div class="absolute top-10 left-1/3 size-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:22px_22px]"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 lg:px-6 pt-12 pb-20 lg:pt-16 lg:pb-24">
        <p class="inline-flex items-center gap-2 text-xs font-semibold bg-white/15 border border-white/20 rounded-full px-3 py-1.5">
            <i class="ki-filled ki-home"></i> Portal Properti Indonesia
        </p>
        <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight max-w-2xl">Temukan Rumah Impianmu dengan Mudah</h1>
        <p class="mt-3 text-white/85 max-w-xl text-sm sm:text-base">Jelajahi properti dijual &amp; disewa di seluruh Indonesia — rumah, apartemen, ruko, tanah, dan villa pilihan.</p>
        <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
            <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="bg-white/15 hover:bg-white/25 border border-white/20 rounded-full px-4 py-2 transition">Dijual</a>
            <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="bg-white/15 hover:bg-white/25 border border-white/20 rounded-full px-4 py-2 transition">Disewa</a>
            <a href="{{ route('projects.index') }}" class="bg-white/15 hover:bg-white/25 border border-white/20 rounded-full px-4 py-2 transition">Proyek Baru</a>
            <a href="{{ route('agents.index') }}" class="bg-white/15 hover:bg-white/25 border border-white/20 rounded-full px-4 py-2 transition">Agen</a>
        </div>
    </div>
</section>

<!-- Search card overlap -->
<div class="max-w-7xl mx-auto px-4 lg:px-6">
    <form method="GET" action="{{ route('properties.index') }}" class="relative -mt-10 lg:-mt-12 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 sm:p-5 grid gap-3 md:grid-cols-[1fr_170px_170px_170px_auto]">
        <label class="block">
            <span class="text-xs font-semibold text-slate-500">Kata kunci / lokasi</span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci, lokasi, atau nama proyek…" class="kt-input mt-1 w-full" />
        </label>
        <label class="block">
            <span class="text-xs font-semibold text-slate-500">Tipe properti</span>
            <select name="type" class="kt-select mt-1 w-full">
                <option value="">Semua Tipe</option>
                @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="text-xs font-semibold text-slate-500">Status</span>
            <select name="status" class="kt-select mt-1 w-full">
                <option value="">Jual / Sewa</option>
                <option value="jual" @selected(request('status') === 'jual')>Dijual</option>
                <option value="sewa" @selected(request('status') === 'sewa')>Disewa</option>
            </select>
        </label>
        <label class="block">
            <span class="text-xs font-semibold text-slate-500">Kota</span>
            <input type="text" name="city" value="{{ request('city') }}" placeholder="cth: Bandung" class="kt-input mt-1 w-full" />
        </label>
        <button type="submit" class="kt-btn kt-btn-primary w-full md:w-auto md:self-end !py-2.5"><i class="ki-filled ki-magnifier"></i> Cari</button>
    </form>
</div>

<!-- Value props (tanpa angka klaim) -->
<section class="max-w-7xl mx-auto px-4 lg:px-6 pt-8">
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-border bg-card p-4 flex items-start gap-3">
            <span class="grid place-items-center size-10 rounded-xl bg-emerald-100 text-emerald-700 shrink-0"><i class="ki-filled ki-shield-tick text-lg"></i></span>
            <div>
                <div class="font-bold text-sm">Listing Terverifikasi</div>
                <p class="text-xs text-muted-foreground mt-0.5">Data agen dan properti melewati kurasi sebelum tampil.</p>
            </div>
        </div>
        <div class="rounded-2xl border border-border bg-card p-4 flex items-start gap-3">
            <span class="grid place-items-center size-10 rounded-xl bg-blue-100 text-blue-700 shrink-0"><i class="ki-filled ki-calculator text-lg"></i></span>
            <div>
                <div class="font-bold text-sm">Simulasi KPR Mudah</div>
                <p class="text-xs text-muted-foreground mt-0.5">Hitung cicilan dan ajukan pembiayaan dari halaman detail.</p>
            </div>
        </div>
        <div class="rounded-2xl border border-border bg-card p-4 flex items-start gap-3">
            <span class="grid place-items-center size-10 rounded-xl bg-amber-100 text-amber-700 shrink-0"><i class="ki-filled ki-auction text-lg"></i></span>
            <div>
                <div class="font-bold text-sm">Ada Jalur Lelang</div>
                <p class="text-xs text-muted-foreground mt-0.5">Ikuti lelang properti transparan dengan riwayat penawaran.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured -->
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="flex items-end justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Properti Unggulan</h2>
            <p class="text-sm text-muted-foreground mt-1">Pilihan terbaik minggu ini, dikurasi untukmu.</p>
        </div>
        <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-primary hover:underline">Lihat Semua →</a>
    </div>
    @if ($featured->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
            Belum ada properti unggulan. Data akan tampil setelah properti dipublikasikan.
        </div>
    @else
        <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featured as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    @endif
</section>

<!-- Cities -->
<section class="border-y border-border bg-muted/50">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
        <div class="flex items-end justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Kota Populer</h2>
                <p class="text-sm text-muted-foreground mt-1">Mulai pencarian dari kota dengan listing terbanyak.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="shrink-0 text-sm font-semibold text-primary hover:underline">Semua Kota →</a>
        </div>
        @php
            $fallbackCities = collect([
                ['city' => 'Jakarta Selatan', 'total' => null],
                ['city' => 'Surabaya', 'total' => null],
                ['city' => 'Bandung', 'total' => null],
                ['city' => 'Bekasi', 'total' => null],
                ['city' => 'Depok', 'total' => null],
                ['city' => 'Jakarta Timur', 'total' => null],
            ]);
            $cityList = $cities->isNotEmpty() ? $cities : $fallbackCities;
            $gradients = [
                'from-blue-600 to-cyan-500',
                'from-violet-600 to-fuchsia-500',
                'from-emerald-600 to-teal-500',
                'from-orange-500 to-amber-500',
                'from-rose-600 to-pink-500',
            ];
        @endphp
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            @foreach ($cityList->take(8) as $i => $row)
                @php
                    $cityName = is_array($row) ? $row['city'] : $row->city;
                    $total = is_array($row) ? ($row['total'] ?? null) : ($row->total ?? null);
                    $grad = $gradients[$i % count($gradients)];
                @endphp
                <a href="{{ route('properties.index', ['city' => $cityName]) }}" class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $grad }} p-4 sm:p-5 min-h-28 flex flex-col justify-end text-white shadow-sm hover:shadow-md transition">
                    <div class="pointer-events-none absolute -top-8 -right-8 size-28 rounded-full border-[14px] border-white/15" aria-hidden="true"></div>
                    <span class="font-bold leading-tight">{{ $cityName }}</span>
                    <span class="mt-1 flex items-center justify-between text-xs text-white/85">
                        <span>{{ $total ? $total . ' properti' : 'Lihat properti' }}</span>
                        <span aria-hidden="true">→</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Agent -->
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="rounded-2xl overflow-hidden grid gap-0 lg:grid-cols-2 bg-primary text-primary-foreground">
        <div class="p-6 sm:p-8 lg:p-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Anda Agen Properti?</h2>
            <p class="opacity-85 mt-2 text-sm sm:text-base">Pasarkan listing Anda ke jutaan pencari properti dan kelola leads dalam satu dasbor. Gratis untuk memulai.</p>
            <ul class="mt-4 space-y-2 text-sm">
                <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle"></i> Pasang listing tak terbatas</li>
                <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle"></i> Terima leads langsung via WhatsApp</li>
                <li class="flex items-center gap-2"><i class="ki-filled ki-check-circle"></i> Pantau performa di dasbor agen</li>
            </ul>
            <a href="{{ route('register') }}" class="mt-5 inline-flex kt-btn bg-white text-black hover:bg-white/90 font-semibold">Daftar Jadi Agen →</a>
        </div>
        <div class="bg-white/10 p-6 sm:p-8 lg:p-10 lg:border-l border-white/15">
            <form method="POST" action="{{ route('leads.store') }}" class="bg-white text-foreground rounded-2xl p-5 shadow-lg grid gap-3">
                @csrf
                <div class="font-bold">Minta dihubungi tim kami</div>
                <input type="text" name="name" required placeholder="Nama" class="kt-input w-full" />
                <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input w-full" />
                <button type="submit" class="kt-btn kt-btn-primary w-full">Daftar Jadi Agen</button>
                <p class="text-[11px] text-muted-foreground text-center">Dengan mendaftar, Anda menyetujui syarat &amp; ketentuan yang berlaku.</p>
            </form>
        </div>
    </div>
</section>
@endsection
