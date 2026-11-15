<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Contracts\View\View;

class AuctionShowController extends Controller
{
    public function show(Auction $auction): View
    {
        $auction->load(['property.images']);

        $bids = $auction->bids()->with('user')->limit(10)->get();

        return view('pages.auctions-show', [
            'auction' => $auction,
            'bids' => $bids,
        ]);
    }
}
