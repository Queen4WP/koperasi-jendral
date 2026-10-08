<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Loan;

class LoanPolicy
{
    public function view(User $user, Loan $loan): bool
    {
        return $user->role === 'admin' || ($user->member && $loan->member_id === $user->member->id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'member' && $user->member !== null;
    }

    public function administrativeAction(User $user): bool
    {
        return $user->role === 'admin';
    }
}
