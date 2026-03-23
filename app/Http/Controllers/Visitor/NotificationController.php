<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Display the notifications page.
     * Note: 'Visitor/Notifications' matches the plural filename.
     */
    public function index()
    {
        return Inertia::render('Visitor/Notifications', [
            'notifications' => auth()->user()->notifications()->latest()->get()
        ]);
    }

    /**
     * Mark a single notification as read.
     * Notifications use UUIDs as primary keys.
     */
    public function markAsRead($id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('status', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications cleared.');
    }
}