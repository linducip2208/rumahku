<?php

namespace App\Filament\Resources\AuctionBids\Pages;

use App\Filament\Resources\AuctionBids\AuctionBidResource;
use Filament\Resources\Pages\ListRecords;

class ListAuctionBids extends ListRecords
{
    protected static string $resource = AuctionBidResource::class;
}
