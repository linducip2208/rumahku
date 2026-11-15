<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Property;
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

        $property = Property::query()->findOrFail($validated['property_id']);

        if ((int) $property->user_id !== (int) $request->user()->id) {
            abort(403, 'Properti bukan milik Anda.');
        }

        $duplicate = PropertyOrder::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->where('status', 'pending')
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['property_id' => 'Order pending untuk properti ini sudah ada.']);
        }

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
