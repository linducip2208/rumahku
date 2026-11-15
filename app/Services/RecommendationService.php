<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Rekomendasi properti terkait: kota + tipe sama, pita harga ±30%.
     * Murni query lokal, tanpa API eksternal.
     *
     * @return Collection<int, Property>
     */
    public function related(Property $property, int $limit = 4): Collection
    {
        $low = (int) floor($property->price * 0.7);
        $high = (int) ceil($property->price * 1.3);

        $primary = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->where('type', $property->type)
            ->whereBetween('price', [$low, $high])
            ->latest()
            ->take($limit)
            ->get();

        if ($primary->count() >= $limit) {
            return $primary;
        }

        $fallback = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('id', '!=', $property->id)
            ->whereNotIn('id', $primary->pluck('id')->all())
            ->where('city', $property->city)
            ->latest()
            ->take($limit - $primary->count())
            ->get();

        return $primary->concat($fallback);
    }
}
