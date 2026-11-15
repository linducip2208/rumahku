<?php

namespace App\Filament\Resources\AuctionBids;

use App\Filament\Resources\AuctionBids\Pages\ListAuctionBids;
use App\Filament\Resources\AuctionBids\Pages\ViewAuctionBid;
use App\Filament\Resources\AuctionBids\Schemas\AuctionBidInfolist;
use App\Filament\Resources\AuctionBids\Tables\AuctionBidsTable;
use App\Models\AuctionBid;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuctionBidResource extends Resource
{
    protected static ?string $model = AuctionBid::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'id';

    public static function infolist(Schema $schema): Schema
    {
        return AuctionBidInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuctionBidsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuctionBids::route('/'),
            'view' => ViewAuctionBid::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
