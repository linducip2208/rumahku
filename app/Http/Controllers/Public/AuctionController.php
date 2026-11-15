<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Contracts\View\View;

class AuctionController extends Controller
{
    public function index(): View
    {
        $auctions = Auction::query()
            ->with('property.images')
            ->where('status', 'open')
            ->orderBy('ends_at')
            ->paginate(12);

        return view('pages.auctions-index', [
            'auctions' => $auctions,
        ]);
    }
}
