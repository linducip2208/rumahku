{{-- Chat stub: daftar pesan properti (read-only). Dipakai manual via @include, show.blade.php TIDAK diubah. --}}
@props(['property'])

<div class="kt-card p-5 mt-4">
    <h2 class="font-bold mb-1">Diskusi Properti</h2>
    <p class="text-xs text-muted-foreground mb-3">Pesan publik terkait listing ini. Kirim pesan belum diimplementasikan.</p>
    @php
        $chatMessages = isset($messages) ? $messages : \App\Models\Message::query()->with('sender')->where('property_id', $property->id)->latest()->take(10)->get();
    @endphp
    @if ($chatMessages->isEmpty())
        <div class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
            Belum ada pesan untuk properti ini.
        </div>
    @else
        <ul class="grid gap-2 text-sm">
            @foreach ($chatMessages as $msg)
                <li class="rounded-xl bg-muted/60 p-3">
                    <div class="text-xs text-muted-foreground">{{ $msg->sender?->name ?? 'Pengunjung' }} &middot; {{ $msg->created_at?->format('d M Y H:i') }}</div>
                    <div class="mt-1">{{ $msg->body }}</div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
