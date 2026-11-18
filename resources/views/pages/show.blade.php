@extends('layouts.metronic')

@php
    $metaTitle = ($property->title ?? 'Detail Properti') . ' — Rumah123';
    $metaDesc = \Illuminate\Support\Str::limit(strip_tags($property->description ?? ''), 160);
    if ($metaDesc === '') {
        $metaDesc = ($property->title ?? 'Detail Properti') . ' — ' . ($property->address ?? '') . ' ' . ($property->city ?? '');
    }
    $canonical = route('properties.show', $property->slug);
    $metaImage = $property->images->first() ? asset('storage/' . $property->images->first()->path) : asset('og-cover.png');

    $isFav = auth()->check()
        ? \App\Models\Favorite::query()->where('user_id', auth()->id())->where('property_id', $property->id)->exists()
        : false;
    $inCompare = in_array($property->id, (array) session('compare_ids', []), true);

    $agentName = optional($property->user)->name ?? 'Agen Rumah123';
    $rawPhone = optional(optional($property->user)->agentProfile)->phone
        ?? optional(optional($property->user)->agentProfile)->wa
        ?? null;
    $waNumber = $rawPhone ? preg_replace('/\D+/', '', (string) $rawPhone) : null;
    if ($waNumber && str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
    $shareUrl = $canonical;
    $waShareHref = 'https://wa.me/?text=' . urlencode($property->title . ' ' . $shareUrl);
    $waAgentHref = $waNumber
        ? 'https://wa.me/' . $waNumber . '?text=' . urlencode('Halo ' . $agentName . ', saya tertarik dengan properti: ' . $property->title . ' (' . $shareUrl . ')')
        : null;
    $photos = $property->images;
    $cover = $photos->first()?->path;
@endphp

@section('title', $metaTitle)

@push('styles')
    <x-seo-meta :title="$metaTitle" :description="$metaDesc" :canonical="$canonical" :image="$metaImage" />
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateListing',
        'name' => $property->title,
        'description' => $metaDesc,
        'url' => $canonical,
        'image' => $metaImage,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $property->address ?? null,
            'addressLocality' => $property->city ?? null,
            'addressRegion' => $property->province ?? null,
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => $property->price,
            'priceCurrency' => 'IDR',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8 pb-24 md:pb-8 grid gap-6 lg:grid-cols-[1fr_340px]">
    <div>
        <!-- Gallery -->
        <div class="kt-card overflow-hidden" x-data="{ current: '{{ $cover ? asset('storage/' . $cover) : '' }}' }">
            <div class="relative aspect-[16/9] bg-muted grid place-items-center text-muted-foreground overflow-hidden">
                @if ($cover)
                    <x-skeleton class="absolute inset-0 size-full !rounded-none" />
                    <img id="prop-main-img" src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="relative size-full object-cover bg-muted animate-pulse" :src="current || '{{ asset('storage/' . $cover) }}'" onload="this.classList.remove('animate-pulse');this.previousElementSibling?.remove()" />
                @else
                    <span class="flex items-center gap-2"><i class="ki-filled ki-picture text-3xl"></i> Foto properti</span>
                @endif
            </div>
            @if ($photos->count() > 1)
                <div class="grid grid-cols-4 gap-2 p-3">
                    @foreach ($photos->take(8) as $i => $photo)
                        @php $full = asset('storage/' . $photo->path); @endphp
                        <button type="button"
                            data-full="{{ $full }}"
                            @click="current = '{{ $full }}'"
                            onclick="document.getElementById('prop-main-img').src = this.dataset.full;"
                            class="rounded-lg overflow-hidden ring-offset-2 focus:ring-2 focus:ring-primary bg-muted animate-pulse"
                            aria-label="Lihat foto {{ $i + 1 }}">
                            <img src="{{ $full }}" alt="Foto {{ $i + 1 }} {{ $property->title }}" class="aspect-[4/3] w-full object-cover bg-muted animate-pulse hover:opacity-90 transition" loading="lazy" onload="this.classList.remove('animate-pulse')" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Price + specs -->
        <div class="kt-card p-5 mt-4">
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-wide">
                <span class="px-2 py-1 rounded bg-primary/10 text-primary">{{ $property->listing_status === 'jual' ? 'Dijual' : 'Disewa' }}</span>
                <span class="px-2 py-1 rounded bg-muted text-muted-foreground">{{ ucfirst($property->type) }}</span>
                @if (! empty($property->certificate))
                    <span class="px-2 py-1 rounded bg-muted text-muted-foreground">Sertifikat: {{ $property->certificate }}</span>
                @endif
            </div>
            <h1 class="text-2xl font-extrabold mt-2">{{ $property->title }}</h1>
            <div class="text-sm text-muted-foreground mt-1">
                {{ $property->address }} — {{ $property->district }}, {{ $property->city }}
                &middot; <a class="text-primary hover:underline" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ $property->lat && $property->lng ? $property->lat . ',' . $property->lng : urlencode($property->address . ' ' . $property->city) }}">Lihat Peta</a>
            </div>
            <div class="text-2xl font-extrabold text-primary mt-3">Rp {{ number_format($property->price, 0, ',', '.') }}</div>

            <!-- Actions: favorit / bandingkan / share -->
            <div class="flex flex-wrap items-center gap-2 mt-4">
                @auth
                    <form method="POST" action="{{ route('favorites.toggle', $property) }}">
                        @csrf
                        <button type="submit" class="kt-btn {{ $isFav ? 'kt-btn-primary' : 'kt-btn-outline' }}" aria-pressed="{{ $isFav ? 'true' : 'false' }}">
                            <i class="ki-filled ki-heart"></i> {{ $isFav ? 'Favorit ✓' : 'Favorit' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-heart"></i> Favorit</a>
                @endauth

                <form method="POST" action="{{ route('compare.add', $property) }}">
                    @csrf
                    <button type="submit" class="kt-btn {{ $inCompare ? 'kt-btn-primary' : 'kt-btn-outline' }}" {{ $inCompare ? 'disabled' : '' }}>
                        <i class="ki-filled ki-arrows-horizontal"></i> {{ $inCompare ? 'Sudah Dibandingkan' : 'Bandingkan +' }}
                    </button>
                </form>

                <button type="button" id="share-copy" data-url="{{ $shareUrl }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-copy"></i> <span id="share-copy-label">Salin Link</span>
                </button>
                <a href="{{ $waShareHref }}" target="_blank" rel="noopener" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-share"></i> Bagikan WA
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-sm">
                <div class="rounded-xl bg-muted/60 p-3"><div class="text-muted-foreground text-xs">Kamar Tidur</div><div class="font-bold">{{ $property->bedrooms ?? '-' }}</div></div>
                <div class="rounded-xl bg-muted/60 p-3"><div class="text-muted-foreground text-xs">Kamar Mandi</div><div class="font-bold">{{ $property->bathrooms ?? '-' }}</div></div>
                <div class="rounded-xl bg-muted/60 p-3"><div class="text-muted-foreground text-xs">Luas Tanah</div><div class="font-bold">{{ $property->land_area ?? '-' }} m²</div></div>
                <div class="rounded-xl bg-muted/60 p-3"><div class="text-muted-foreground text-xs">Luas Bangunan</div><div class="font-bold">{{ $property->building_area ?? '-' }} m²</div></div>
            </div>
        </div>

        <!-- Description -->
        <div class="kt-card p-5 mt-4">
            <h2 class="font-bold mb-2">Deskripsi</h2>
            <div class="text-sm text-muted-foreground whitespace-pre-line">{{ $property->description }}</div>
        </div>

        @php
            $facilities = $property->facilities;
            if (is_string($facilities)) {
                $facilities = array_filter(array_map('trim', explode(',', $facilities)));
            }
            $facilities = is_array($facilities) ? array_values(array_filter($facilities, fn ($f) => trim((string) $f) !== '')) : [];
        @endphp
        @if (! empty($facilities))
            <div class="kt-card p-5 mt-4">
                <h2 class="font-bold mb-3">Fasilitas</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($facilities as $facility)
                        <span class="inline-flex items-center gap-1.5 text-xs rounded-full bg-primary/10 text-primary px-3 py-1.5 font-medium">
                            <i class="ki-filled ki-check-circle"></i> {{ $facility }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($property->lat && $property->lng)
            @php
                $lat = (float) $property->lat;
                $lng = (float) $property->lng;
                $d = 0.02;
                $bbox = ($lng - $d) . ',' . ($lat - $d) . ',' . ($lng + $d) . ',' . ($lat + $d);
            @endphp
            <div class="kt-card p-5 mt-4">
                <h2 class="font-bold mb-1">Lokasi</h2>
                <p class="text-xs text-muted-foreground mb-3">{{ $property->address }} — {{ $property->district }}, {{ $property->city }}</p>
                <div class="rounded-xl overflow-hidden border border-border aspect-[16/9] bg-muted">
                    <iframe
                        title="Peta lokasi {{ $property->title }}"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&layer=mapnik&marker={{ $lat }},{{ $lng }}"
                        class="size-full border-0"
                        loading="lazy"></iframe>
                </div>
            </div>
        @endif

        <!-- KPR Calculator -->
        <div class="kt-card p-5 mt-4">
            <h2 class="font-bold mb-1">Kalkulator KPR</h2>
            <p class="text-xs text-muted-foreground mb-3">Estimasi cicilan bulanan (anuitas).</p>
            <div class="grid gap-2 sm:grid-cols-3">
                <label class="text-xs">Harga (Rp)<input id="kpr-price" type="number" value="{{ $property->price }}" class="kt-input mt-1" /></label>
                <label class="text-xs">DP (%)<input id="kpr-dp" type="number" value="20" min="0" max="90" class="kt-input mt-1" /></label>
                <label class="text-xs">Tenor (thn)<input id="kpr-years" type="number" value="20" min="1" max="30" class="kt-input mt-1" /></label>
                <label class="text-xs">Bunga (%/thn)<input id="kpr-rate" type="number" value="8" step="0.1" class="kt-input mt-1" /></label>
                <div class="sm:col-span-2 flex items-end"><button type="button" id="kpr-calc" class="kt-btn kt-btn-primary w-full">Hitung Cicilan</button></div>
            </div>
            <div id="kpr-result" class="mt-3 text-sm font-semibold">Cicilan: —</div>

            <hr class="my-4 border-border" />
            <h3 class="font-bold text-sm">Ajukan KPR Properti Ini</h3>
            <p class="text-xs text-muted-foreground mb-3">Isi form di bawah, tim kami akan menghubungi Anda.</p>
            @if (session('success'))
                <div class="rounded-lg bg-primary/10 text-primary text-sm px-3 py-2 mb-3">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('kpr.store') }}" class="grid gap-2 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}" />
                <input type="text" name="name" required placeholder="Nama lengkap" class="kt-input" value="{{ old('name') }}" />
                <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input" value="{{ old('phone') }}" />
                <input type="email" name="email" placeholder="Email (opsional)" class="kt-input" value="{{ old('email') }}" />
                <input type="number" name="dp_amount" required min="0" max="{{ $property->price }}" placeholder="Uang muka (Rp, maks {{ number_format($property->price, 0, ',', '.') }})" class="kt-input" value="{{ old('dp_amount') }}" />
                <select name="tenor_months" required class="kt-input">
                    <option value="">— Pilih tenor —</option>
                    @foreach ([12, 24, 36, 60, 120, 180, 240] as $tenor)
                        <option value="{{ $tenor }}" @selected(old('tenor_months') == $tenor)>{{ $tenor }} bulan ({{ $tenor / 12 }} thn)</option>
                    @endforeach
                </select>
                <select name="bank_preference" class="kt-input">
                    <option value="">— Bank preferensi (opsional) —</option>
                    @foreach (['mandiri' => 'Mandiri', 'bca' => 'BCA', 'bri' => 'BRI', 'bni' => 'BNI', 'syariah' => 'Syariah'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('bank_preference') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="number" name="monthly_income" min="0" placeholder="Penghasilan / bulan (Rp, opsional)" class="kt-input sm:col-span-2" value="{{ old('monthly_income') }}" />
                <textarea name="notes" rows="2" placeholder="Catatan (opsional)" class="kt-input sm:col-span-2">{{ old('notes') }}</textarea>
                @error('dp_amount')
                    <p class="text-xs text-destructive sm:col-span-2">{{ $message }}</p>
                @enderror
                @error('tenor_months')
                    <p class="text-xs text-destructive sm:col-span-2">{{ $message }}</p>
                @enderror
                <div class="sm:col-span-2"><button type="submit" class="kt-btn kt-btn-primary w-full">Ajukan KPR</button></div>
            </form>
        </div>

        <!-- Diskusi -->
        @php($messages = \App\Models\Message::query()->with('sender')->where('property_id', $property->id)->latest()->take(10)->get())
        @include('components.chat-thread', ['property' => $property, 'messages' => $messages])

        <!-- Related -->
        @if ($related->isNotEmpty())
            <h2 class="font-bold mt-6 mb-3">Properti Serupa di {{ $property->city }}</h2>
            <div class="grid gap-3 sm:gap-4 grid-cols-2">
                @foreach ($related as $rel)
                    <x-property-card :property="$rel" />
                @endforeach
            </div>
        @endif
    </div>

    <!-- Agent card + lead form -->
    <aside>
        <div class="kt-card p-5 lg:sticky lg:top-20">
            <div class="flex items-center gap-3">
                <span class="grid place-items-center size-12 rounded-full bg-primary/10 text-primary font-bold">{{ strtoupper(substr($agentName, 0, 1)) }}</span>
                <div>
                    <div class="font-bold">{{ $agentName }}</div>
                    <div class="text-xs text-muted-foreground">Agen terverifikasi</div>
                </div>
            </div>
            @if ($waAgentHref || $waNumber)
                <div class="grid grid-cols-2 gap-2 mt-4">
                    @if ($waNumber)
                        <a href="{{ route('track.click', [$property, 'tel']) }}" class="kt-btn kt-btn-outline w-full"><i class="ki-filled ki-phone"></i> Telepon</a>
                    @endif
                    @if ($waAgentHref)
                        <a href="{{ route('track.click', [$property, 'wa']) }}" target="_blank" rel="noopener" class="kt-btn kt-btn-primary w-full"><i class="ki-filled ki-message-text"></i> WhatsApp</a>
                    @endif
                </div>
            @endif
            <form id="lead-form" method="POST" action="{{ route('leads.store') }}" class="mt-4 grid gap-2 scroll-mt-24">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}" />
                <input type="text" name="name" required placeholder="Nama" class="kt-input" value="{{ old('name') }}" />
                <input type="text" name="phone" required placeholder="No. HP / WA" class="kt-input" value="{{ old('phone') }}" />
                <textarea name="message" rows="3" placeholder="Pesan untuk agen…" class="kt-input">{{ old('message', 'Saya tertarik dengan properti ini. Mohon info lebih lanjut.') }}</textarea>
                <div class="cf-turnstile" data-sitekey="{{ \App\Models\Setting::get('captcha.turnstile_site_key', '') }}"></div>
                @error('captcha')
                    <p class="text-xs text-destructive">{{ $message }}</p>
                @enderror
                @if (\App\Models\Setting::get('captcha.turnstile_site_key', ''))
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                @endif
                <button type="submit" class="kt-btn kt-btn-primary w-full">Hubungi Agen</button>
            </form>
            <a href="{{ route('properties.brochure', $property->slug) }}" class="kt-btn kt-btn-outline w-full mt-2"><i class="ki-filled ki-document"></i> Unduh Brosur (PDF)</a>
        </div>
    </aside>
</div>

<!-- Sticky bar mobile -->
<div class="fixed bottom-0 inset-x-0 z-40 md:hidden border-t border-border bg-background/95 backdrop-blur px-3 py-2 grid grid-cols-3 gap-2">
    @if ($waNumber)
        <a href="{{ route('track.click', [$property, 'tel']) }}" class="kt-btn kt-btn-outline w-full"><i class="ki-filled ki-phone"></i> Telepon</a>
    @else
        <span class="kt-btn kt-btn-outline w-full opacity-50 pointer-events-none"><i class="ki-filled ki-phone"></i> Telepon</span>
    @endif
    @if ($waAgentHref)
        <a href="{{ route('track.click', [$property, 'wa']) }}" target="_blank" rel="noopener" class="kt-btn kt-btn-primary w-full"><i class="ki-filled ki-message-text"></i> WA</a>
    @else
        <span class="kt-btn kt-btn-outline w-full opacity-50 pointer-events-none"><i class="ki-filled ki-message-text"></i> WA</span>
    @endif
    <a href="#lead-form" class="kt-btn kt-btn-outline w-full"><i class="ki-filled ki-messages"></i> Tanya</a>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
<script>
document.getElementById('kpr-calc')?.addEventListener('click', function () {
    const price = parseFloat(document.getElementById('kpr-price').value) || 0;
    const dpPct = parseFloat(document.getElementById('kpr-dp').value) || 0;
    const years = parseInt(document.getElementById('kpr-years').value) || 0;
    const rate = parseFloat(document.getElementById('kpr-rate').value) || 0;
    const out = document.getElementById('kpr-result');
    if (!price || !years) { out.textContent = 'Cicilan: —'; return; }
    const principal = price * (1 - dpPct / 100);
    const r = rate / 100 / 12;
    const n = years * 12;
    const monthly = r > 0 ? principal * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1) : principal / n;
    out.textContent = 'Cicilan: Rp ' + Math.round(monthly).toLocaleString('id-ID') + ' / bulan';
});
document.getElementById('share-copy')?.addEventListener('click', async function () {
    const url = this.dataset.url;
    const label = document.getElementById('share-copy-label');
    try {
        await navigator.clipboard.writeText(url);
    } catch (e) {
        const ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }
    if (label) {
        label.textContent = 'Tersalin!';
        setTimeout(() => { label.textContent = 'Salin Link'; }, 2000);
    }
});
</script>
@endpush
