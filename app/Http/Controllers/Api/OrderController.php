<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Property;
use App\Models\PropertyOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rows = PropertyOrder::query()
            ->with('property:id,title,slug')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return response()->json($rows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'package_slug' => ['required', 'string', 'exists:packages,slug'],
        ]);

        $property = Property::query()->findOrFail($validated['property_id']);
        $package = Package::query()->where('slug', $validated['package_slug'])->firstOrFail();

        if ((int) $property->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Properti bukan milik Anda.'], 403);
        }

        $order = PropertyOrder::create([
            'user_id' => $request->user()->id,
            'property_id' => $property->id,
            'package_slug' => $package->slug,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Order berhasil dibuat.',
            'id' => $order->id,
            'amount' => $order->amount,
            'status' => $order->status,
        ], 201);
    }
}
