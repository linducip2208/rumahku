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
<header class="bg-[#0F2A4A] text-white sticky top-0 z-30 shadow-md" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <span class="grid place-items-center size-9 rounded-lg bg-white text-[#0F2A4A] font-extrabold text-lg">R</span>
                <span class="font-extrabold text-xl tracking-tight text-white">Rumah123</span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium" aria-label="Navigasi utama">
                <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ __('site.nav.jual') }}</a>
                <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ __('site.nav.sewa') }}</a>
                <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ __('site.nav.proyek') }}</a>
                <a href="{{ route('agents.index') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ __('site.nav.agen') }}</a>
                <a href="{{ route('info.show', 'tentang') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ __('site.footer.perusahaan') }}</a>
            </nav>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ url()->current().'?lang=id' }}" class="{{ app()->getLocale() === 'id' ? 'text-amber-300' : 'text-white/70 hover:text-white hover:underline' }}">ID</a>
                    <span class="text-white/40">|</span>
                    <a href="{{ url()->current().'?lang=en' }}" class="{{ app()->getLocale() === 'en' ? 'text-amber-300' : 'text-white/70 hover:text-white hover:underline' }}">EN</a>
                </div>
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center rounded-lg border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/10 transition">{{ __('site.actions.masuk') }}</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1 rounded-lg bg-amber-400 px-3 sm:px-4 py-2 text-sm font-bold text-[#0F2A4A] hover:bg-amber-300 transition">
                        <i class="ki-filled ki-plus"></i><span class="hidden sm:inline"> {{ __('site.actions.pasang_iklan') }}</span><span class="sm:hidden"> Iklan</span>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center rounded-lg border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/10 transition">{{ __('site.actions.dasbor') }}</a>
                    <a href="{{ route('agent.listings.create') }}" class="inline-flex items-center gap-1 rounded-lg bg-amber-400 px-3 sm:px-4 py-2 text-sm font-bold text-[#0F2A4A] hover:bg-amber-300 transition">
                        <i class="ki-filled ki-plus"></i><span class="hidden sm:inline"> {{ __('site.actions.pasang_iklan') }}</span><span class="sm:hidden"> Iklan</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                        @csrf
                        <button type="submit" class="rounded-lg border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/10 transition">{{ __('site.actions.keluar') }}</button>
                    </form>
                @endauth
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-label="Menu navigasi" class="md:hidden inline-flex items-center justify-center size-10 rounded-lg border border-white/20 text-white hover:bg-white/10">
                    <span x-show="!open"><i class="ki-filled ki-burger-menu text-lg"></i></span>
                    <span x-show="open" x-cloak><i class="ki-filled ki-cross text-lg"></i></span>
                </button>
            </div>
        </div>
        <nav x-show="open" x-cloak x-collapse class="md:hidden pb-4 grid gap-1 text-sm font-medium border-t border-white/10 pt-3 mt-1">
            <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.nav.jual') }}</a>
            <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.nav.sewa') }}</a>
            <a href="{{ route('projects.index') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.nav.proyek') }}</a>
            <a href="{{ route('agents.index') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.nav.agen') }}</a>
            <a href="{{ route('info.show', 'tentang') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.footer.perusahaan') }}</a>
            <a href="{{ route('properties.index') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10 hover:text-white">{{ __('site.actions.cari') }}</a>
            <div class="flex items-center gap-2 px-3 pt-2 text-xs font-semibold">
                <a href="{{ url()->current().'?lang=id' }}" class="{{ app()->getLocale() === 'id' ? 'text-amber-300' : 'text-white/70' }}">ID</a>
                <span class="text-white/40">|</span>
                <a href="{{ url()->current().'?lang=en' }}" class="{{ app()->getLocale() === 'en' ? 'text-amber-300' : 'text-white/70' }}">EN</a>
            </div>
            @guest
                <a href="{{ route('login') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10">{{ __('site.actions.masuk') }}</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10">{{ __('site.actions.dasbor') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-white/85 hover:bg-white/10">{{ __('site.actions.keluar') }}</button>
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
            ? $cities->take(6)
            : collect([['city' => 'Jakarta Selatan'], ['city' => 'Depok'], ['city' => 'Bekasi'], ['city' => 'Bandung'], ['city' => 'Surabaya'], ['city' => 'Medan']]);
        $footCategories = [
            ['label' => 'Rumah', 'type' => 'rumah'],
            ['label' => 'Apartemen', 'type' => 'apartemen'],
            ['label' => 'Ruko', 'type' => 'ruko'],
            ['label' => 'Tanah', 'type' => 'tanah'],
            ['label' => 'Villa', 'type' => 'villa'],
            ['label' => 'Kost', 'type' => 'kost'],
        ];
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
            <div class="font-bold mb-3 text-white">Jual &amp; Sewa</div>
            <ul class="space-y-2 text-white/70">
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'jual', 'type' => 'rumah']) }}">Rumah Dijual</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'sewa', 'type' => 'rumah']) }}">Rumah Disewa</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'jual', 'type' => 'apartemen']) }}">Apartemen Dijual</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['status' => 'jual', 'type' => 'tanah']) }}">Tanah Dijual</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('projects.index') }}">Proyek Baru</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('auctions.index') }}">Lelang Properti</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('agents.index') }}">Agen Properti</a></li>
            </ul>
        </div>
        <div>
            <div class="font-bold mb-3 text-white">Kategori</div>
            <ul class="space-y-2 text-white/70">
                @foreach ($footCategories as $fc)
                    <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['type' => $fc['type']]) }}">{{ $fc['label'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <div class="font-bold mb-3 text-white">Lokasi Populer</div>
            <ul class="space-y-2 text-white/70">
                @foreach ($footCities as $row)
                    @php $fc = is_array($row) ? ($row['city'] ?? '') : ($row->city ?? ''); @endphp
                    @if ($fc)
                        <li><a class="hover:text-white hover:underline" href="{{ route('properties.index', ['city' => $fc]) }}">Properti di {{ $fc }}</a></li>
                    @endif
                @endforeach
            </ul>
            <div class="font-bold mt-5 mb-3 text-white">Bantuan</div>
            <ul class="space-y-2 text-white/70">
                <li><a class="hover:text-white hover:underline" href="{{ route('info.show', 'tentang') }}">Tentang Kami</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('info.show', 'bantuan') }}">Bantuan</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('contact') }}">Kontak</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('info.show', 'privasi') }}">Kebijakan Privasi</a></li>
                <li><a class="hover:text-white hover:underline" href="{{ route('info.show', 'syarat') }}">Syarat &amp; Ketentuan</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 lg:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/60">
            <div>© {{ date('Y') }} Rumah123. Hak cipta dilindungi.</div>
            <div class="flex items-center gap-4">
                <a href="{{ route('info.show', 'privasi') }}" class="hover:text-white hover:underline">Kebijakan Privasi</a>
                <a href="{{ route('info.show', 'syarat') }}" class="hover:text-white hover:underline">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('metronic/js/core.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
