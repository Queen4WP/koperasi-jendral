<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Saving;

class SavingPolicy
{
    public function view(User $user, Saving $saving): bool
    {
        return $user->role === 'admin' || ($user->member && $saving->member_id === $user->member->id);
    }

    public function pay(User $user, Saving $saving): bool
    {
        return $user->role === 'admin' || ($user->member && $saving->member_id === $user->member->id);
    }
}
