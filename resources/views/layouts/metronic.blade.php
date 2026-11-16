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
<header class="border-b border-border bg-background sticky top-0 z-30" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <span class="grid place-items-center size-9 rounded-lg bg-primary text-primary-foreground font-extrabold text-lg">R</span>
                <span class="font-extrabold text-xl tracking-tight">Rumah<span class="text-primary">123</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2 rounded-lg hover:bg-muted">{{ __('site.nav.jual') }}</a>
                <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2 rounded-lg hover:bg-muted">{{ __('site.nav.sewa') }}</a>
                <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-lg hover:bg-muted">{{ __('site.nav.proyek') }}</a>
                <a href="{{ route('agents.index') }}" class="px-3 py-2 rounded-lg hover:bg-muted">{{ __('site.nav.agen') }}</a>
                <a href="{{ route('articles.index') }}" class="px-3 py-2 rounded-lg hover:bg-muted">{{ __('site.nav.artikel') }}</a>
            </nav>
            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ url()->current().'?lang=id' }}" class="{{ app()->getLocale() === 'id' ? 'text-primary' : 'text-muted-foreground hover:underline' }}">ID</a>
                    <span class="text-muted-foreground">|</span>
                    <a href="{{ url()->current().'?lang=en' }}" class="{{ app()->getLocale() === 'en' ? 'text-primary' : 'text-muted-foreground hover:underline' }}">EN</a>
                </div>
                <a href="{{ route('properties.index') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">{{ __('site.actions.cari') }}</a>
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">{{ __('site.actions.masuk') }}</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex kt-btn kt-btn-primary">{{ __('site.actions.daftar') }}</a>
                    <a href="{{ route('register') }}" class="sm:hidden kt-btn kt-btn-primary !px-3">Daftar</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">{{ __('site.actions.dasbor') }}</a>
                    <a href="{{ route('agent.listings.create') }}" class="kt-btn kt-btn-primary !px-3 sm:!px-4">
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
            <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.nav.jual') }}</a>
            <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.nav.sewa') }}</a>
            <a href="{{ route('projects.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.nav.proyek') }}</a>
            <a href="{{ route('agents.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.nav.agen') }}</a>
            <a href="{{ route('articles.index') }}" class="px-3 py-2.5 rounded-lg hover:bg-muted">{{ __('site.nav.artikel') }}</a>
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

<footer class="border-t border-border mt-12">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-10 grid gap-8 md:grid-cols-4 text-sm">
        <div>
            <div class="font-extrabold text-lg mb-2">Rumah<span class="text-primary">123</span></div>
            <p class="text-muted-foreground">{{ __('site.footer.tagline') }}</p>
        </div>
        <div>
            <div class="font-semibold mb-2">{{ __('site.footer.jelajahi') }}</div>
            <ul class="space-y-1 text-muted-foreground">
                <li><a class="hover:underline" href="{{ route('properties.index', ['status' => 'jual']) }}">{{ __('site.nav.jual') }}</a></li>
                <li><a class="hover:underline" href="{{ route('properties.index', ['status' => 'sewa']) }}">{{ __('site.nav.sewa') }}</a></li>
                <li><a class="hover:underline" href="{{ route('projects.index') }}">{{ __('site.nav.proyek') }}</a></li>
            </ul>
        </div>
        <div>
            <div class="font-semibold mb-2">{{ __('site.footer.perusahaan') }}</div>
            <ul class="space-y-1 text-muted-foreground">
                <li><a class="hover:underline" href="{{ route('agents.index') }}">{{ __('site.nav.agen') }}</a></li>
                <li><a class="hover:underline" href="{{ route('articles.index') }}">{{ __('site.nav.artikel') }}</a></li>
                <li><a class="hover:underline" href="{{ route('home') }}">{{ __('site.footer.tentang') }}</a></li>
            </ul>
        </div>
        <div id="cta-lead">
            <div class="font-semibold mb-2">{{ __('site.footer.pasang_iklan') }}</div>
            <p class="text-muted-foreground mb-3">{{ __('site.footer.cta_text') }}</p>
            <form method="POST" action="{{ route('leads.store') }}" class="flex flex-col gap-2">
                @csrf
                <input type="text" name="name" required placeholder="{{ __('site.footer.nama') }}" class="kt-input" />
                <input type="text" name="phone" required placeholder="{{ __('site.footer.telepon') }}" class="kt-input" />
                <button class="kt-btn kt-btn-primary" type="submit">{{ __('site.footer.hubungi') }}</button>
            </form>
        </div>
    </div>
    <div class="border-t border-border py-4 text-center text-xs text-muted-foreground">© {{ date('Y') }} Rumah123. {{ __('site.footer.hak_cipta') }}</div>
</footer>

<script src="{{ asset('metronic/js/core.bundle.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
