<?php

namespace App\Filament\Resources\KprApplications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KprApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pemohon')->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('phone')->label('Telepon'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('user.name')->label('Akun Pengguna'),
                TextEntry::make('monthly_income')->money('IDR')->label('Penghasilan Bulanan'),
            ])->columns(2),
            Section::make('Pengajuan')->schema([
                TextEntry::make('property.title')->label('Properti'),
                TextEntry::make('dp_amount')->money('IDR')->label('Uang Muka (DP)'),
                TextEntry::make('tenor_months')->suffix(' bulan')->label('Tenor'),
                TextEntry::make('bank_preference')->badge()->label('Bank Preferensi'),
                TextEntry::make('status')->badge()->label('Status'),
                TextEntry::make('notes')->columnSpanFull()->label('Catatan'),
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Diajukan'),
            ])->columns(2),
        ]);
    }
}
