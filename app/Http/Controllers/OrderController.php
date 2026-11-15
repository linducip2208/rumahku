<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PropertyOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'package_slug' => ['required', 'string', 'exists:packages,slug'],
        ]);

        $package = Package::query()->where('slug', $validated['package_slug'])->firstOrFail();

        PropertyOrder::query()->create([
            'user_id' => $request->user()->id,
            'property_id' => $validated['property_id'],
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Order paket dibuat, menunggu verifikasi pembayaran admin.');
    }
}
