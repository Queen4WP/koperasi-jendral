<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LoanInstallment;

class LoanInstallmentPolicy
{
    public function view(User $user, LoanInstallment $installment): bool
    {
        return $user->role === 'admin' || ($user->member && $installment->loan->member_id === $user->member->id);
    }

    public function pay(User $user, LoanInstallment $installment): bool
    {
        return $user->role === 'admin' || ($user->member && $installment->loan->member_id === $user->member->id);
    }
}
