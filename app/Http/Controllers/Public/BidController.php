<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function store(Request $request, Auction $auction): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        $auction->placeBid($request->user(), $validated['amount']);

        return back()->with('success', 'Tawaran Anda sebesar Rp '.number_format($validated['amount'], 0, ',', '.').' berhasil dipasang.');
    }
}
