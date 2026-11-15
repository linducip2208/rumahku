<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PropertyCardResource;
use App\Models\Favorite;
use App\Models\Property;
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
        $validated = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
        ]);

        $favorite = Favorite::query()->where([
            'user_id' => $request->user()->id,
            'property_id' => $validated['property_id'],
        ])->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json(['favorited' => false]);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'property_id' => $validated['property_id'],
        ]);

        return response()->json(['favorited' => true], 201);
    }
}
