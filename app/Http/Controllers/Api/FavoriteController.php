<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PropertyCardResource;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::query()
            ->with(['images', 'user.agentProfile'])
            ->whereIn('id', $request->user()->favorites()->select('property_id'))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return PropertyCardResource::collection($properties);
    }

    public function toggle(Request $request): JsonResponse
    {
        if ($request->user()->tokenCant('favorites:write')) {
            abort(403, 'Token tidak memiliki ability favorites:write.');
        }

        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
        ]);

        $property = Property::query()->findOrFail($validated['property_id']);

        $existing = Favorite::query()->where([
            'user_id' => $request->user()->id,
            'property_id' => $validated['property_id'],
        ])->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['favorited' => false]);
        }

        if ($property->published_at === null || $property->sold_at !== null) {
            return response()->json(['message' => 'Properti tidak tersedia.'], 422);
        }

        try {
            $favorite = Favorite::query()->firstOrCreate([
                'user_id' => $request->user()->id,
                'property_id' => $validated['property_id'],
            ]);
        } catch (QueryException $e) {
            // Race: baris sudah dibuat request konkuren — anggap sudah favorit.
            if (($e->errorInfo[0] ?? null) === '23000') {
                return response()->json(['favorited' => true]);
            }

            throw $e;
        }

        if (! $favorite->wasRecentlyCreated) {
            // Dibuat request konkuren di antara cek dan insert — anggap sudah favorit.
            return response()->json(['favorited' => true]);
        }

        return response()->json(['favorited' => true], 201);
    }
}
