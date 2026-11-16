<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Rumah123 — Portal properti Indonesia: jual, sewa, proyek baru." />
    <title>@yield('title', 'Rumah123 — Jual, Sewa & Proyek Baru')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="theme-color" content="#0b54d6" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="{{ asset('metronic/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('metronic/css/styles.css') }}" rel="stylesheet" />
    @stack('styles')
</head>
<body class="antialiased flex flex-col min-h-screen text-base text-foreground bg-background">
<script>
    const defaultThemeMode = 'light';
    let themeMode;
    if (document.documentElement) {
        if (localStorage.getItem('kt-theme')) {
            themeMode = localStorage.getItem('kt-theme');
        } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
            themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
        } else {
            themeMode = defaultThemeMode;
        }
        if (themeMode === 'system') {
            themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.classList.add(themeMode);
    }
</script>

<!-- Header / Topbar -->
<header class="border-b border-border bg-background/95 backdrop-blur sticky top-0 z-30" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <span class="grid place-items-center size-9 rounded-lg bg-blue-600 text-white font-extrabold text-lg">R</span>
                <span class="font-extrabold text-xl tracking-tight text-[#0F2A4A]">Rumah<span class="text-blue-600">123</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navigasi utama">
                <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2 rounded-lg text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">{{ __('site.nav.jual') }}</a>
                <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2 rounded-lg text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">{{ __('site.nav.sewa') }}</a>
                <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-lg text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">{{ __('site.nav.proyek') }}</a>
                <a href="{{ route('agents.index') }}" class="px-3 py-2 rounded-lg text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">{{ __('site.nav.agen') }}</a>
                <a href="{{ route('articles.index') }}" class="px-3 py-2 rounded-lg text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">{{ __('site.nav.artikel') }}</a>
            </nav>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ url()->current().'?lang=id' }}" class="{{ app()->getLocale() === 'id' ? 'text-blue-600' : 'text-muted-foreground hover:underline' }}">ID</a>
                    <span class="text-muted-foreground">|</span>
                    <a href="{{ url()->current().'?lang=en' }}" class="{{ app()->getLocale() === 'en' ? 'text-blue-600' : 'text-muted-foreground hover:underline' }}">EN</a>
                </div>
                <a href="{{ route('properties.index') }}" aria-label="Cari properti" class="hidden sm:inline-flex items-center justify-center size-10 rounded-lg border border-border text-[#0F2A4A] hover:bg-blue-50 hover:text-blue-700">
                    <i class="ki-filled ki-magnifier text-lg"></i>
                </a>
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">{{ __('site.actions.masuk') }}</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex kt-btn kt-btn-primary !bg-blue-600 hover:!bg-blue-700">{{ __('site.actions.daftar') }}</a>
                    <a href="{{ route('register') }}" class="sm:hidden kt-btn kt-btn-primary !bg-blue-600 hover:!bg-blue-700 !px-3">Daftar</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">{{ __('site.actions.dasbor') }}</a>
                    <a href="{{ route('agent.listings.create') }}" class="kt-btn kt-btn-primary !bg-blue-600 hover:!bg-blue-700 !px-3 sm:!px-4">
                        <i class="ki-filled ki-plus"></i><span class="hidden sm:inline"> {{ __('site.actions.pasang_iklan') }}</span><span class="sm:hidden"> Iklan</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                        @csrf
                        <button type="submit" class="kt-btn kt-btn-outline">{{ __('site.actions.keluar') }}</button>
                    </form>
                @endauth
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-label="Menu navigasi" class="md:hidden inline-flex items-center justify-center size-10 rounded-lg border border-border hover:bg-muted">
                    <span x-show="!open"><i class="ki-filled ki-burger-menu text-lg"></i></span>
                    <span x-show="open" x-cloak><i class="ki-filled ki-cross text-lg"></i></span>
                </button>
            </div>
        </div>
        <nav x-show="open" x-cloak x-collapse class="md:hidden pb-4 grid gap-1 text-sm font-medium border-t border-border pt-3 mt-1">
            <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.nav.jual') }}</a>
            <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.nav.sewa') }}</a>
            <a href="{{ route('projects.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.nav.proyek') }}</a>
            <a href="{{ route('agents.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.nav.agen') }}</a>
            <a href="{{ route('articles.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.nav.artikel') }}</a>
            <a href="{{ route('properties.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-blue-50">{{ __('site.actions.cari') }}</a>
            <div class="flex items-center gap-2 px-3 pt-2 text-xs font-semibold">
                <a href="{{ url()->current().'?lang=id' }}" class="{{ app()->getLocale() === 'id' ? 'text-primary' : 'text-muted-foreground' }}">ID</a>
                <span class="text-muted-foreground">|</span>
                <a href="{{ url()->current().'?lang=en' }}" class="{{ app()->getLocale() === 'en' ? 'text-primary' : 'text-muted-foreground' }}">EN</a>
            </div>
            @guest
                <a href="{{ route('login') }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.actions.masuk') }}</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.actions.dasbor') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.actions.keluar') }}</button>
                </form>
            @endauth
        </nav>
    </div>
</header>

<!-- Flash -->
@if (session('success'))
    <div class="max-w-7xl mx-auto px-4 lg:px-6 w-full mt-4">
        <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="max-w-7xl mx-auto px-4 lg:px-6 w-full mt-4">
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<main class="grow">
    @yield('content')
</main>

<footer class="bg-[#0F2A4A] text-white mt-12">
    @php
        $footLanding = $landing ?? null;
        $footGet = function (string $key, mixed $default = null) use ($footLanding): mixed {
            if ($footLanding === null) {
                return $default;
            }
            if (is_array($footLanding)) {
                return $footLanding[$key] ?? $default;
            }
            return $footLanding->{$key} ?? $default;
        };
        $footAbout = $footGet('footer_about', 'Portal properti Indonesia: cari rumah, apartemen, tanah, ruko, dan villa dijual & disewa, proyek baru, dan agen terverifikasi.');
        $footCities = (isset($cities) && $cities instanceof \Illuminate\Support\Collection && $cities->isNotEmpty())
            ? $cities->take(5)
            : collect([['city' => 'Jakarta Selatan'], ['city' => 'Depok'], ['city' => 'Bekasi'], ['city' => 'Bandung'], ['city' => 'Surabaya']]);
        $socials = [
            'facebook' => $footGet('social_facebook'),
            'instagram' => $footGet('social_instagram'),
            'twitter' => $footGet('social_twitter'),
            'youtube' => $footGet('social_youtube'),
            'tiktok' => $footGet('social_tiktok'),
            'linkedin' => $footGet('social_linkedin'),
        ];
        $socials = array_filter($socials);
    @endphp
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-12 grid gap-10 md:grid-cols-2 lg:grid-cols-4 text-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="grid place-items-center size-9 rounded-lg bg-blue-600 text-white font-extrabold text-lg">R</span>
                <span class="font-extrabold text-xl tracking-tight">Rumah<span class="text-blue-400">123</span></span>
            </div>
            <p class="mt-3 text-white/70">{{ $footAbout }}</p>
            @if (! empty($socials))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($socials as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="grid place-items-center size-9 rounded-full bg-white/10 hover:bg-blue-600 transition text-white/90">
                            <i class="ki-filled ki-{{ $network === 'twitter' ? 'twitter' : $network }} text-base"></i>
                        </a>
                    @endforeach
                </div>
            @endif
            @if ($footGet('contact_phone') || $footGet('contact_email') || $footGet('contact_address'))
                <div class="mt-4 space-y-1 text-white/70 text-[13px]">
                    @if ($footGet('contact_phone'))<div>Telp: {{ $footGet('contact_phone') }}</div>@endif
                    @if ($footGet('contact_email'))<div>Email: {{ $footGet('contact_email') }}</div>@endif
                    @if ($footGet('contact_address'))<div>{{ $footGet('contact_address') }}</div>@endif
                </div>
            @endif
        </div>
        <div>
            <div class="font-bold mb-3 text-white">Jual & Sewa</div>
            <ul class="space-y-2 text-white/70">
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'jual']) }}">Rumah Dijual</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'sewa']) }}">Rumah Disewa</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('projects.index') }}">Proyek Baru</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('auctions.index') }}">Lelang Properti</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('agents.index') }}">Agen Properti</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('articles.index') }}">Artikel & Panduan</a></li>
            </ul>
        </div>
        <div>
            <div class="font-bold mb-3 text-white">Kategori</div>
            <ul class="space-y-2 text-white/70">
                @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'ruko' => 'Ruko', 'tanah' => 'Tanah', 'villa' => 'Villa'] as $val => $label)
                    <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['type' => $val]) }}">{{ $label }}</a></li>
                @endforeach
            </ul>
            <div class="font-bold mt-5 mb-3 text-white">Lokasi Populer</div>
            <ul class="space-y-2 text-white/70">
                @foreach ($footCities as $row)
                    @php $fc = is_array($row) ? ($row['city'] ?? '') : ($row->city ?? ''); @endphp
                    @if ($fc)
                        <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['city' => $fc]) }}">Properti di {{ $fc }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div id="cta-lead">
            <div class="font-bold mb-3 text-white">Butuh Bantuan?</div>
            <ul class="space-y-2 text-white/70 mb-4">
                <li><a class="hover:text-white hover:underline" href="{{ route('pricing') }}">Harga & Paket</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('compare.index') }}">Bandingkan Properti</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('services.index') }}">Jasa Properti</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('register') }}">Pasang Iklan Gratis</a></li>
            </ul>
            <p class="text-white/70 mb-3 text-[13px]">Tinggalkan kontak, tim kami akan menghubungi Anda.</p>
            <form method="POST" action="{{ route('leads.store') }}" class="flex flex-col gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama" class="kt-input !bg-white/10 !border-white/20 !text-white placeholder:!text-white/50" />
                <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input !bg-white/10 !border-white/20 !text-white placeholder:!text-white/50" />
                <button class="kt-btn !bg-blue-600 hover:!bg-blue-500 !text-white font-semibold" type="submit">Hubungi Kami</button>
            </form>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/60">
            <div>© {{ date('Y') }} Rumah123. Hak cipta dilindungi.</div>
            <div class="flex items-center gap-4">
                <span class="hover:text-white cursor-pointer">Kebijakan Privasi</span>
                <span class="hover:text-white cursor-pointer">Syarat & Ketentuan</span>
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('metronic/js/core.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
