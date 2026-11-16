<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PropertyCardResource;
use App\Http\Resources\Api\PropertyDetailResource;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:rumah,apartemen,tanah,ruko,villa'],
            'status' => ['nullable', 'in:jual,sewa'],
            'city' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'sort' => ['nullable', 'in:latest,price_asc,price_desc'],
        ]);

        $query = Property::query()
            ->with(['images', 'user.agentProfile'])
            ->whereNotNull('published_at')
            ->whereNull('sold_at');

        if (! empty($validated['q'])) {
            $keyword = $validated['q'];
            $query->where(function ($inner) use ($keyword): void {
                $inner->where('title', 'like', "%{$keyword}%")
                    ->orWhere('district', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (! empty($validated['status'])) {
            $query->where('listing_status', $validated['status']);
        }

        if (! empty($validated['city'])) {
            $query->where('city', 'like', '%'.$validated['city'].'%');
        }

        if (isset($validated['min_price'])) {
            $query->where('price', '>=', $validated['min_price']);
        }

        if (isset($validated['max_price'])) {
            $query->where('price', '<=', $validated['max_price']);
        }

        if (isset($validated['bedrooms'])) {
            $query->where('bedrooms', '>=', $validated['bedrooms']);
        }

        match ($validated['sort'] ?? 'latest') {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };

        $properties = $query->paginate(15)->withQueryString();

        return PropertyCardResource::collection($properties);
    }

    public function show(string $slug)
    {
        $property = Property::query()
            ->with(['images', 'user.agentProfile'])
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->firstOrFail();

        $property->increment('views');

        $property->related_ids = Property::query()
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->where('type', $property->type)
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->orderByDesc('published_at')
            ->limit(6)
            ->pluck('id')
            ->all();

        return new PropertyDetailResource($property);
    }
}
