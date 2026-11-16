<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pesan')->schema([
                TextEntry::make('property.title')->label('Properti'),
                TextEntry::make('sender.name')->label('Pengirim'),
                TextEntry::make('body')->columnSpanFull()->label('Isi pesan'),
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Waktu'),
            ])->columns(2),
        ]);
    }
}
