<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function view(User $user, Lead $lead): bool
    {
        return (int) $lead->agent_user_id === (int) $user->id;
    }

    public function update(User $user, Lead $lead): bool
    {
        return (int) $lead->agent_user_id === (int) $user->id;
    }
}
