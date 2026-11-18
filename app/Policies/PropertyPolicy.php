<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function view(User $user, Property $property): bool
    {
        if ($property->sold_at !== null || $property->published_at === null) {
            return $user->role === 'admin' || (int) $property->user_id === (int) $user->id;
        }

        return true;
    }

    public function update(User $user, Property $property): bool
    {
        return (int) $property->user_id === (int) $user->id;
    }

    public function delete(User $user, Property $property): bool
    {
        return (int) $property->user_id === (int) $user->id;
    }
}
