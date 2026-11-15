<!DOCTYPE html>
<html class="h-full" lang="id" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr">
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
<header class="border-b border-border bg-background sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <span class="grid place-items-center size-9 rounded-lg bg-primary text-primary-foreground font-extrabold text-lg">R</span>
                <span class="font-extrabold text-xl tracking-tight">Rumah<span class="text-primary">123</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('properties.index', ['status' => 'jual']) }}" class="px-3 py-2 rounded-lg hover:bg-muted">Jual</a>
                <a href="{{ route('properties.index', ['status' => 'sewa']) }}" class="px-3 py-2 rounded-lg hover:bg-muted">Sewa</a>
                <a href="{{ route('properties.index', ['type' => 'apartemen']) }}" class="px-3 py-2 rounded-lg hover:bg-muted">Proyek Baru</a>
                <a href="{{ route('agents.index') }}" class="px-3 py-2 rounded-lg hover:bg-muted">Agen</a>
                <a href="{{ route('articles.index') }}" class="px-3 py-2 rounded-lg hover:bg-muted">Artikel</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('properties.index') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">Cari Properti</a>
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="kt-btn kt-btn-primary">Daftar Agen</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex kt-btn kt-btn-outline">Dasbor</a>
                    <a href="{{ route('agent.listings.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i> Pasang Iklan
                    </a>
                @endauth
            </div>
        </div>
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
            <p class="text-muted-foreground">Portal properti Indonesia: jual, sewa, proyek baru, dan agen terpercaya.</p>
        </div>
        <div>
            <div class="font-semibold mb-2">Jelajahi</div>
            <ul class="space-y-1 text-muted-foreground">
                <li><a class="hover:underline" href="{{ route('properties.index', ['status' => 'jual']) }}">Jual</a></li>
                <li><a class="hover:underline" href="{{ route('properties.index', ['status' => 'sewa']) }}">Sewa</a></li>
                <li><a class="hover:underline" href="{{ route('properties.index') }}">Proyek Baru</a></li>
            </ul>
        </div>
        <div>
            <div class="font-semibold mb-2">Perusahaan</div>
            <ul class="space-y-1 text-muted-foreground">
                <li><a class="hover:underline" href="{{ route('properties.index') }}">Agen</a></li>
                <li><a class="hover:underline" href="{{ route('properties.index') }}">Artikel</a></li>
                <li><a class="hover:underline" href="{{ route('home') }}">Tentang Kami</a></li>
            </ul>
        </div>
        <div id="cta-lead">
            <div class="font-semibold mb-2">Pasang Iklan</div>
            <p class="text-muted-foreground mb-3">Punya properti? Pasarkan ke jutaan pencari.</p>
            <form method="POST" action="{{ route('leads.store') }}" class="flex flex-col gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama" class="kt-input" />
                <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input" />
                <button class="kt-btn kt-btn-primary" type="submit">Hubungi Saya</button>
            </form>
        </div>
    </div>
    <div class="border-t border-border py-4 text-center text-xs text-muted-foreground">© {{ date('Y') }} Rumah123. All rights reserved.</div>
</footer>

<script src="{{ asset('metronic/js/core.bundle.js') }}"></script>
@stack('scripts')
</body>
</html>
