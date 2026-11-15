@extends('layouts.metronic')

@section('title', ($property->exists ? 'Ubah' : 'Tambah') . ' Listing — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">{{ $property->exists ? 'Ubah Listing' : 'Tambah Listing' }}</h1>
        <p class="text-sm text-muted-foreground mt-1">Lengkapi detail properti Anda. Foto maksimal 5MB (JPG/PNG/WebP).</p>

        <form method="POST" action="{{ $property->exists ? route('agent.listings.update', $property) : route('agent.listings.store') }}" enctype="multipart/form-data" class="kt-card p-6 mt-6 grid gap-4">
            @csrf
            @if ($property->exists)
                @method('PUT')
            @endif

            <div>
                <label for="title" class="text-sm font-semibold">Judul</label>
                <input id="title" type="text" name="title" value="{{ old('title', $property->title) }}" required maxlength="255" placeholder="Cth: Rumah Dijual di Kebayoran Baru, Jakarta Selatan" class="kt-input mt-1 w-full" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="type" class="text-sm font-semibold">Tipe</label>
                    <select id="type" name="type" required class="kt-select mt-1 w-full">
                        @foreach (['rumah' => 'Rumah', 'apartemen' => 'Apartemen', 'tanah' => 'Tanah', 'ruko' => 'Ruko', 'villa' => 'Villa'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('type', $property->type) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="listing_status" class="text-sm font-semibold">Status Listing</label>
                    <select id="listing_status" name="listing_status" required class="kt-select mt-1 w-full">
                        <option value="jual" @selected(old('listing_status', $property->listing_status) === 'jual')>Dijual</option>
                        <option value="sewa" @selected(old('listing_status', $property->listing_status) === 'sewa')>Disewa</option>
                    </select>
                </div>
                <div>
                    <label for="price" class="text-sm font-semibold">Harga (Rp)</label>
                    <input id="price" type="number" name="price" value="{{ old('price', $property->price) }}" required min="0" step="1" placeholder="1500000000" class="kt-input mt-1 w-full" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="bedrooms" class="text-sm font-semibold">Kamar Tidur</label>
                    <input id="bedrooms" type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" min="0" class="kt-input mt-1 w-full" />
                </div>
                <div>
                    <label for="bathrooms" class="text-sm font-semibold">Kamar Mandi</label>
                    <input id="bathrooms" type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" min="0" class="kt-input mt-1 w-full" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="land_area" class="text-sm font-semibold">Luas Tanah (m²)</label>
                    <input id="land_area" type="number" name="land_area" value="{{ old('land_area', $property->land_area) }}" min="0" step="0.01" class="kt-input mt-1 w-full" />
                </div>
                <div>
                    <label for="building_area" class="text-sm font-semibold">Luas Bangunan (m²)</label>
                    <input id="building_area" type="number" name="building_area" value="{{ old('building_area', $property->building_area) }}" min="0" step="0.01" class="kt-input mt-1 w-full" />
                </div>
                <div>
                    <label for="certificate" class="text-sm font-semibold">Sertifikat</label>
                    <input id="certificate" type="text" name="certificate" value="{{ old('certificate', $property->certificate) }}" maxlength="50" placeholder="SHM / HGB / AJB" class="kt-input mt-1 w-full" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="province" class="text-sm font-semibold">Provinsi</label>
                    <input id="province" type="text" name="province" value="{{ old('province', $property->province) }}" required maxlength="255" class="kt-input mt-1 w-full" />
                </div>
                <div>
                    <label for="city" class="text-sm font-semibold">Kota</label>
                    <input id="city" type="text" name="city" value="{{ old('city', $property->city) }}" required maxlength="255" class="kt-input mt-1 w-full" />
                </div>
                <div>
                    <label for="district" class="text-sm font-semibold">Kecamatan</label>
                    <input id="district" type="text" name="district" value="{{ old('district', $property->district) }}" maxlength="255" class="kt-input mt-1 w-full" />
                </div>
            </div>

            <div>
                <label for="address" class="text-sm font-semibold">Alamat Lengkap</label>
                <textarea id="address" name="address" required rows="2" class="kt-input mt-1 w-full">{{ old('address', $property->address) }}</textarea>
            </div>

            <div>
                <label for="description" class="text-sm font-semibold">Deskripsi</label>
                <textarea id="description" name="description" required rows="5" class="kt-input mt-1 w-full">{{ old('description', $property->description) }}</textarea>
            </div>

            @if ($property->exists && $property->images->isNotEmpty())
                <div>
                    <span class="text-sm font-semibold">Foto Saat Ini</span>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-2">
                        @foreach ($property->images as $image)
                            <label class="relative block rounded-xl overflow-hidden border border-border cursor-pointer">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Foto properti" class="aspect-square w-full object-cover" loading="lazy" />
                                <span class="absolute bottom-1 left-1 right-1 flex items-center gap-1 rounded-lg bg-black/60 text-white text-[11px] px-2 py-1">
                                    <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="kt-checkbox" />
                                    Hapus
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">Centang foto yang ingin dihapus.</p>
                </div>
            @endif

            <div>
                <label for="images" class="text-sm font-semibold">{{ $property->exists ? 'Tambah Foto' : 'Foto Properti' }}</label>
                <input id="images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="kt-input mt-1 w-full" />
                <p class="text-xs text-muted-foreground mt-1">Bisa pilih banyak file. Format JPG/PNG/WebP, maks 5MB per file.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="kt-btn kt-btn-primary">{{ $property->exists ? 'Simpan Perubahan' : 'Buat Listing' }}</button>
                <a href="{{ route('agent.listings.index') }}" class="kt-btn kt-btn-outline">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
