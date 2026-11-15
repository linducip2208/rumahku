@extends('layouts.metronic')

@section('title', 'Daftar Agen — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="max-w-md mx-auto kt-card p-6 lg:p-8">
        <h1 class="text-2xl font-extrabold tracking-tight">Daftar Jadi Agen</h1>
        <p class="text-sm text-muted-foreground mt-1">Pasarkan listing Anda ke jutaan pencari properti.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 grid gap-4">
            @csrf
            <div>
                <label for="name" class="text-sm font-semibold">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda" class="kt-input mt-1 w-full" />
            </div>
            <div>
                <label for="email" class="text-sm font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="kt-input mt-1 w-full" />
            </div>
            <div>
                <label for="password" class="text-sm font-semibold">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="kt-input mt-1 w-full" />
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-semibold">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" class="kt-input mt-1 w-full" />
            </div>
            <button type="submit" class="kt-btn kt-btn-primary w-full">Daftar</button>
        </form>

        <p class="text-sm text-muted-foreground mt-4 text-center">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">Masuk</a>
        </p>
    </div>
</section>
@endsection
