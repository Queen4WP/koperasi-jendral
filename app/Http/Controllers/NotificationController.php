<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        Gate::authorize('update', $notification);

        $this->notificationService->markAsRead($notification, auth()->user());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Mark all unread notifications for the authenticated user as read.
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(auth()->user());

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'updated_count' => $count]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
