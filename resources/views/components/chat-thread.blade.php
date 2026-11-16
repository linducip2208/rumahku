{{-- Thread diskusi properti. Menerima $property + $messages (10 terbaru, eager sender). --}}
@props(['property', 'messages'])

<div class="kt-card p-5 mt-4">
    <h2 class="font-bold mb-1">Diskusi Properti</h2>
    <p class="text-xs text-muted-foreground mb-3">Pesan publik terkait listing ini.</p>

    @if (session('chat_success'))
        <div class="rounded-lg bg-primary/10 text-primary text-sm px-3 py-2 mb-3">{{ session('chat_success') }}</div>
    @endif

    @if ($messages->isEmpty())
        <div class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
            Belum ada pesan untuk properti ini. Jadilah yang pertama bertanya.
        </div>
    @else
        <ul class="grid gap-2 text-sm">
            @foreach ($messages as $msg)
                <li class="rounded-xl bg-muted/60 p-3">
                    <div class="text-xs text-muted-foreground">{{ $msg->sender?->name ?? 'Pengunjung' }} &middot; {{ $msg->created_at?->format('d M Y H:i') }}</div>
                    <div class="mt-1">{{ $msg->body }}</div>
                </li>
            @endforeach
        </ul>
    @endif

    @auth
        <form method="POST" action="{{ route('messages.store') }}" class="mt-4 grid gap-2">
            @csrf
            <input type="hidden" name="property_id" value="{{ $property->id }}" />
            <textarea name="body" rows="3" required maxlength="1000" placeholder="Tulis pertanyaan tentang properti ini…" class="kt-input">{{ old('body') }}</textarea>
            @error('body')
                <p class="text-xs text-destructive">{{ $message }}</p>
            @enderror
            <button type="submit" class="kt-btn kt-btn-primary w-full">Kirim Pesan</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="kt-btn kt-btn-outline w-full mt-4">Masuk untuk ikut diskusi</a>
    @endauth
</div>
