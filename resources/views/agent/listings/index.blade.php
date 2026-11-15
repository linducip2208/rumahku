@extends('layouts.metronic')

@section('title', 'Listing Saya — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">Listing Saya</h1>
            <p class="text-sm text-muted-foreground mt-1">{{ $properties->total() }} properti terdaftar.</p>
        </div>
        <a href="{{ route('agent.listings.create') }}" class="kt-btn kt-btn-primary"><i class="ki-filled ki-plus"></i> Tambah Listing</a>
    </div>

    @if ($properties->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada listing.
            <a href="{{ route('agent.listings.create') }}" class="text-primary hover:underline font-semibold">Buat listing pertama Anda</a>.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-6">
            @foreach ($properties as $property)
                @php $cover = $property->images->first()?->path; @endphp
                <div class="kt-card overflow-hidden">
                    <div class="aspect-[4/3] bg-muted grid place-items-center text-muted-foreground text-sm">
                        @if ($cover)
                            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $property->title }}" class="size-full object-cover" loading="lazy" />
                        @else
                            <i class="ki-filled ki-home text-3xl"></i>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-primary font-extrabold">Rp {{ number_format($property->price, 0, ',', '.') }}</div>
                        <div class="font-semibold mt-1 line-clamp-1">{{ $property->title }}</div>
                        <div class="text-xs text-muted-foreground mt-1">
                            {{ ucfirst($property->type) }} · {{ $property->listing_status === 'jual' ? 'Dijual' : 'Disewa' }} · {{ $property->city }} · {{ number_format($property->views) }} views
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <a href="{{ route('agent.listings.edit', $property) }}" class="kt-btn kt-btn-outline kt-btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('agent.listings.destroy', $property) }}" onsubmit="return confirm('Hapus listing ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="kt-btn kt-btn-sm border border-red-200 text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $properties->links() }}</div>
    @endif
</section>
@endsection
