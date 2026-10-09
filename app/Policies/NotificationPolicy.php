<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * Determine whether the user can view the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can update (mark as read) the notification.
     */
    public function update(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can delete the notification.
     * Deletion is prohibited under the KOPIN immutable notification policy.
     */
    public function delete(User $user, Notification $notification): bool
    {
        return false;
    }
}
