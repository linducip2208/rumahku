<?php

namespace App\Http\Resources\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin User */
class AgentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->relationLoaded('agentProfile')
            ? $this->agentProfile
            : $this->agentProfile()->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'agency' => $profile?->agency,
            'phone' => $profile?->phone,
            'wa' => $profile?->wa,
            'photo' => $profile?->photo ? Storage::url($profile->photo) : null,
            'verified_at' => $profile?->verified_at?->toIso8601String(),
        ];
    }
}
