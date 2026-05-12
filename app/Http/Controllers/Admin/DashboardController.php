<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the main Admin Dashboard overview.
     */
    public function allBookings()
    {
        return Inertia::render('Admin/Dashboard', [
            // Get recent activity
            'bookings' => Booking::with(['user', 'hall'])->latest()->take(10)->get(),
            
            // Stats for the three cards
            'stats' => [
                // 1. Total Registered Users (excluding admins)
                'total_users'      => User::where('role', '!=', 'admin')->count(),
                
                // 2. Total Bookings from database
                'total_bookings'   => Booking::count(),
                
                // 3. Pending Status count from bookings table
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                
                // Keep total visitors if needed for the first card
                'total_visitors'   => Booking::sum('number_of_visitors') ?? 0,
            ]
        ]);
    }
}