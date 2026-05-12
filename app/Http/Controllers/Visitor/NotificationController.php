<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $announcements = $user->announcements()
            ->where('is_active', true)
            ->orderBy('announcement_user.created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content, // Changed from 'message' to 'content'
                    'type' => $item->type ?? 'warning',
                    'target_date' => $item->target_date,
                    'reschedule_date' => $item->reschedule_date,
                    'is_read' => (bool)$item->pivot->is_read,
                    'created_at' => $item->created_at->toIso8601String(), // Send raw date for frontend formatting
                ];
            });

        return Inertia::render('Visitor/Notification', [
            'announcements' => $announcements,
        ]);
    }

    public function markAllAsRead()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->announcements()->updateExistingPivot(
            $user->announcements()->pluck('announcements.id'), 
            ['is_read' => true]
        );

        return redirect()->back();
    }

    public function destroy($id)
    {
        Auth::user()->announcements()->detach($id);
        return redirect()->back();
    }

    public function destroyAll()
    {
        Auth::user()->announcements()->detach();
        return redirect()->back();
    }
}