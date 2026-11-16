@extends('layouts.metronic')

@section('title', 'Hubungi Kami — Rumah123')

@push('styles')
    <x-seo-meta
        title="Hubungi Kami — Rumah123"
        description="Hubungi tim Rumah123 untuk pertanyaan, masukan, atau bantuan seputar properti."
        :canonical="route('contact')"
    />
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground flex items-center gap-1.5" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
        <span aria-hidden="true">/</span>
        <span class="text-foreground font-semibold">Kontak</span>
    </nav>

    <h1 class="mt-3 text-2xl font-extrabold tracking-tight">Hubungi Kami</h1>
    <p class="mt-1 text-sm text-muted-foreground max-w-2xl">Ada pertanyaan atau masukan? Isi formulir di bawah dan tim kami akan segera menghubungi Anda.</p>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-[1fr_320px]">
        <form method="POST" action="{{ route('contact.store') }}" class="kt-card p-5 grid gap-3">
            @csrf
            <label class="block">
                <span class="text-xs font-semibold text-slate-500">Nama lengkap</span>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Anda" class="kt-input mt-1 w-full" />
                @error('name')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-semibold text-slate-500">Nomor telepon / WA</span>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="kt-input mt-1 w-full" />
                    @error('phone')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-500">Email (opsional)</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="kt-input mt-1 w-full" />
                    @error('email')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                </label>
            </div>
            <label class="block">
                <span class="text-xs font-semibold text-slate-500">Subjek (opsional)</span>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Topik pesan" class="kt-input mt-1 w-full" />
                @error('subject')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <label class="block">
                <span class="text-xs font-semibold text-slate-500">Pesan</span>
                <textarea name="message" rows="5" placeholder="Tulis pesan Anda…" class="kt-input mt-1 w-full">{{ old('message') }}</textarea>
                @error('message')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <div>
                <button type="submit" class="kt-btn kt-btn-primary">Kirim Pesan</button>
            </div>
        </form>

        <aside class="kt-card p-5 h-fit">
            <h2 class="font-bold">Informasi Kontak</h2>
            <dl class="mt-3 grid gap-2 text-sm">
                @if ($phone)
                    <div><dt class="text-xs font-semibold text-slate-500">Telepon</dt><dd>{{ $phone }}</dd></div>
                @endif
                @if ($email)
                    <div><dt class="text-xs font-semibold text-slate-500">Email</dt><dd>{{ $email }}</dd></div>
                @endif
                @if ($address)
                    <div><dt class="text-xs font-semibold text-slate-500">Alamat</dt><dd>{{ $address }}</dd></div>
                @endif
                @if (! $phone && ! $email && ! $address)
                    <p class="text-muted-foreground">Informasi kontak akan segera diperbarui.</p>
                @endif
            </dl>
        </aside>
    </div>
</div>
@endsection
