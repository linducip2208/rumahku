<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Leads baru{{ $property ? ': '.$property->title : ' (umum)' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 4px;">{{ $property ? 'Leads baru untuk properti Anda' : 'Leads baru (umum)' }}</h2>
    <p style="margin-top: 0;">{{ $property ? 'Ada calon pembeli/penyewa yang menghubungi melalui Rumah123.' : 'Ada pertanyaan umum yang masuk melalui Rumah123.' }}</p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr>
            <td style="font-weight: bold; width: 140px;">Properti</td>
            <td>{{ $property?->title ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama</td>
            <td>{{ $lead->name }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Telepon</td>
            <td>{{ $lead->phone }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pesan</td>
            <td>{{ $lead->message ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Sumber</td>
            <td>{{ $lead->source ?? '-' }}</td>
        </tr>
    </table>

    @if ($waNumber !== '')
        <p style="margin-top: 16px;">
            <a href="https://wa.me/{{ $waNumber }}">Hubungi via WhatsApp</a>
        </p>
    @endif
</body>
</html>
