<?php

namespace App\Http\Resources\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Property */
class PropertyDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->orderBy('sort')->get();

        $agent = $this->relationLoaded('user') ? $this->user : $this->user()->first();
        $profile = $agent?->relationLoaded('agentProfile')
            ? $agent->agentProfile
            : $agent?->agentProfile()->first();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'type' => $this->type,
            'listing_status' => $this->listing_status,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'land_area' => $this->land_area,
            'building_area' => $this->building_area,
            'certificate' => $this->certificate,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'description' => $this->description,
            'featured' => (bool) $this->featured,
            'views' => $this->views,
            'published_at' => $this->published_at?->toIso8601String(),
            'images' => $images->map(fn ($image): array => [
                'id' => $image->id,
                'url' => Storage::url($image->path),
                'sort' => $image->sort,
            ])->values()->all(),
            'agent' => $agent ? [
                'id' => $agent->id,
                'name' => $agent->name,
                'agency' => $profile?->agency,
                'phone' => $profile?->phone,
                'wa' => $profile?->wa,
                'photo' => $profile?->photo ? Storage::url($profile->photo) : null,
                'verified_at' => $profile?->verified_at?->toIso8601String(),
            ] : null,
            'related_ids' => $this->when(
                isset($this->related_ids),
                $this->related_ids ?? []
            ),
        ];
    }
}
