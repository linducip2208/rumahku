@extends('layouts.metronic')

@section('title', __('site.auth.register_title').' — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="max-w-md mx-auto kt-card p-6 lg:p-8">
        <h1 class="text-2xl font-extrabold tracking-tight">{{ __('site.auth.register_title') }}</h1>
        <p class="text-sm text-muted-foreground mt-1">{{ __('site.auth.register_subtitle') }}</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 grid gap-4">
            @csrf
            <div>
                <label for="name" class="text-sm font-semibold">{{ __('site.auth.name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda" class="kt-input mt-1 w-full" />
                @error('name')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="text-sm font-semibold">{{ __('site.auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="kt-input mt-1 w-full" />
                @error('email')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="text-sm font-semibold">{{ __('site.auth.password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="kt-input mt-1 w-full" />
                @error('password')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-semibold">{{ __('site.auth.confirm') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" class="kt-input mt-1 w-full" />
            </div>
            <div>
                <div class="cf-turnstile" data-sitekey="{{ \App\Models\Setting::get('captcha.turnstile_site_key', '') }}"></div>
                @error('captcha')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
                @if (\App\Models\Setting::get('captcha.turnstile_site_key', ''))
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                @endif
            </div>
            <button type="submit" class="kt-btn kt-btn-primary w-full">{{ __('site.auth.register_submit') }}</button>
        </form>

        <p class="text-sm text-muted-foreground mt-4 text-center">
            {{ __('site.auth.register_prompt') }}
            <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">{{ __('site.auth.register_cta') }}</a>
        </p>
    </div>
</section>
@endsection
