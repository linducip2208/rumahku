<?php

namespace App\Filament\Resources\AuctionBids\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuctionBidInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tawaran')->schema([
                TextEntry::make('auction.id')->label('Lelang'),
                TextEntry::make('auction.property.title')->label('Properti'),
                TextEntry::make('user.name')->label('Penawar'),
                TextEntry::make('amount')->money('IDR')->label('Nominal'),
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Waktu'),
            ])->columns(2),
        ]);
    }
}
