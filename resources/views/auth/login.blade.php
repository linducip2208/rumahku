@extends('layouts.metronic')

@section('title', __('site.auth.login_title').' — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="max-w-md mx-auto kt-card p-6 lg:p-8">
        <h1 class="text-2xl font-extrabold tracking-tight">{{ __('site.auth.login_title') }}</h1>
        <p class="text-sm text-muted-foreground mt-1">{{ __('site.auth.login_subtitle') }}</p>

        @if (session('info'))
            <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('info') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 grid gap-4">
            @csrf
            <div>
                <label for="email" class="text-sm font-semibold">{{ __('site.auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com" class="kt-input mt-1 w-full" />
                @error('email')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="text-sm font-semibold">{{ __('site.auth.password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="kt-input mt-1 w-full" />
                @error('password')
                    <p class="text-xs text-destructive mt-1">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-muted-foreground">
                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="kt-checkbox" />
                {{ __('site.auth.remember') }}
            </label>
            <button type="submit" class="kt-btn kt-btn-primary w-full">{{ __('site.auth.login_submit') }}</button>
        </form>

        <p class="text-sm text-muted-foreground mt-4 text-center">
            {{ __('site.auth.login_prompt') }}
            <a href="{{ route('register') }}" class="text-primary hover:underline font-semibold">{{ __('site.auth.login_cta') }}</a>
        </p>
    </div>
</section>
@endsection
