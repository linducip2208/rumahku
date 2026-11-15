<?php

namespace App\Http\Resources\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Property */
class PropertyCardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstImage = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->orderBy('sort')->first();

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
            'city' => $this->city,
            'district' => $this->district,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'land_area' => $this->land_area,
            'building_area' => $this->building_area,
            'thumbnail' => $firstImage ? Storage::url($firstImage->path) : null,
            'featured' => (bool) $this->featured,
            'agent' => $agent ? [
                'name' => $agent->name,
                'agency' => $profile?->agency,
                'photo' => $profile?->photo ? Storage::url($profile->photo) : null,
            ] : null,
        ];
    }
}
