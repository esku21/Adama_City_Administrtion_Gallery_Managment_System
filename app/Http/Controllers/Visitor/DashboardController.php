<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the visitor dashboard with stats, bookings, and feedback status.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // 1. CHECK: Verify if feedback already exists for this user.
        // Used to toggle the "Send Feedback" nav link and the success banner.
        $hasSubmittedFeedback = Feedback::where('user_id', $user->id)->exists();
        
        return Inertia::render('Visitor/Dashboard', [
            'hasSubmittedFeedback' => (bool) $hasSubmittedFeedback,
            
            // STATS SECTION
            'stats' => [
                'pendingVisits'   => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
                'completedVisits' => Booking::where('user_id', $user->id)
                                        ->whereIn('status', ['Approved', 'completed'])
                                        ->count(),
                'totalBookings'   => Booking::where('user_id', $user->id)->count(),
            ],
            
            // RECENT ACTIVITY SECTION
            'bookings' => Booking::where('user_id', $user->id)
                ->with('halls') // Ensure your Booking model has a 'halls' relationship
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($b) => [
                    'id'             => $b->id,
                    'booking_date'   => $b->booking_date,
                    'slot_id'        => $b->slot_id,
                    'status'         => $b->status,
                    'admin_feedback' => $b->admin_feedback,
                    // Renamed to 'hall_names' to match your Vue template
                    'hall_names'     => $b->halls->name ?? 'N/A', 
                ]),
            
            // Explicitly passing flash messages if not handled by Middleware
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
        ]);
    }

    /**
     * Show only bookings that have received feedback/notes FROM the admin.
     */
    public function feedback(): Response
    {
        $bookingsWithFeedback = Booking::where('user_id', Auth::id())
            ->whereNotNull('admin_feedback')
            ->with('halls')
            ->latest()
            ->get()
            ->map(fn($b) => [
                'id'             => $b->id,
                'booking_date'   => $b->booking_date,
                'admin_feedback' => $b->admin_feedback,
                'hall_name'      => $b->halls->name ?? 'N/A',
            ]);

        return Inertia::render('Visitor/Feedback', [
            'feedbacks' => $bookingsWithFeedback
        ]);
    }
}