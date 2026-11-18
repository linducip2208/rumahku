<?php

namespace App\Policies;

use App\Models\PropertyOrder;
use App\Models\User;

class PropertyOrderPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function view(User $user, PropertyOrder $order): bool
    {
        return (int) $order->user_id === (int) $user->id;
    }

    public function update(User $user, PropertyOrder $order): bool
    {
        return (int) $order->user_id === (int) $user->id;
    }
}
