<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>{{ $property->title }} — Rumah123</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0F2A4A; font-size: 12px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .price { font-size: 18px; font-weight: bold; color: #1d4ed8; }
        .muted { color: #64748b; }
        table.specs { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.specs td { border: 1px solid #cbd5e1; padding: 6px 8px; }
        table.specs td:first-child { font-weight: bold; width: 180px; background: #f1f5f9; }
        .footer { margin-top: 24px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>{{ $property->title }}</h1>
    <p class="muted">{{ ucfirst($property->listing_status ?? 'jual') }} &middot; {{ ucfirst($property->type ?? '') }} &middot; {{ $property->city ?? '' }}{{ $property->district ? ', '.$property->district : '' }}</p>
    <p class="price">Rp {{ number_format((int) $property->price, 0, ',', '.') }}{{ ($property->listing_status ?? '') === 'sewa' ? '/thn' : '' }}</p>
    <table class="specs">
        <tr><td>Kamar Tidur</td><td>{{ $property->bedrooms ?? '-' }}</td></tr>
        <tr><td>Kamar Mandi</td><td>{{ $property->bathrooms ?? '-' }}</td></tr>
        <tr><td>Luas Tanah</td><td>{{ $property->land_area ?? '-' }} m&sup2;</td></tr>
        <tr><td>Luas Bangunan</td><td>{{ $property->building_area ?? '-' }} m&sup2;</td></tr>
        <tr><td>Sertifikat</td><td>{{ $property->certificate ?? '-' }}</td></tr>
        <tr><td>Alamat</td><td>{{ $property->address ?? '-' }}</td></tr>
        @if (! empty($property->facilities))
            <tr><td>Fasilitas</td><td>{{ implode(', ', (array) $property->facilities) }}</td></tr>
        @endif
    </table>
    <h3>Deskripsi</h3>
    <p>{{ $property->description }}</p>
    <p class="muted">Kontak agen: {{ optional($property->user)->name ?? '-' }}</p>
    <p class="footer">Dicetak dari Rumah123 pada {{ now()->format('d M Y H:i') }}. Info dapat berubah; verifikasi langsung dengan pemasang iklan.</p>
</body>
</html>
