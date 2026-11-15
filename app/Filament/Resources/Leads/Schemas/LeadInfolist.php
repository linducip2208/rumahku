<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kontak')->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('phone')->label('Telepon'),
                TextEntry::make('source')->label('Sumber'),
                TextEntry::make('agent.name')->label('Agen'),
            ])->columns(2),
            Section::make('Lead')->schema([
                TextEntry::make('property.title')->label('Properti'),
                TextEntry::make('message')->columnSpanFull()->label('Pesan'),
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Dibuat'),
            ])->columns(2),
        ]);
    }
}
