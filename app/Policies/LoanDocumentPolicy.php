<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LoanDocument;

class LoanDocumentPolicy
{
    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, LoanDocument $document): bool
    {
        // Admin can view any document
        if ($user->role === 'admin') {
            return true;
        }

        // Member can view only if they own the loan the document is attached to
        if ($user->role === 'member' && $user->member) {
            $loan = $document->loan;
            if ($loan && $loan->member_id === $user->member->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can verify (approve/reject) the document.
     */
    public function verify(User $user, LoanDocument $document): bool
    {
        return $user->role === 'admin';
    }
}
