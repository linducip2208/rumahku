<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pesan kontak')->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('phone')->label('Telepon'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('subject')->label('Subjek'),
                TextEntry::make('message')->columnSpanFull()->label('Isi pesan'),
                TextEntry::make('is_read')->formatStateUsing(fn (bool $state): string => $state ? 'Sudah dibaca' : 'Belum dibaca')->label('Status'),
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Waktu masuk'),
            ])->columns(2),
        ]);
    }
}
