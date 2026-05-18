<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Booking; // Added to fetch visitor data
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    /**
     * Display the system reports and analytics page.
     * Updated to fetch Total Visitors from the bookings table.
     */
    public function index()
    {
        /**
         * 1. Calculate Total Visitors.
         * We sum the 'number_of_visitors' column from the bookings table.
         * This ensures the number updates automatically as new bookings are made.
         */
        $totalVisitors = Booking::sum('number_of_visitors');

        /**
         * 2. Fetch Satisfaction count from Feedback table.
         * Counts entries where the sentiment is 'Satisfaction'.
         */
        $totalSatisfaction = Feedback::where('sentiment_status', 'Satisfaction')->count();

        /**
         * 3. Render the Inertia page.
         * The 'totalVisitors' key matches the prop expectation in your Vue component.
         */
        return Inertia::render('Reports/Index', [
            'stats' => [
                'totalVisitors'     => $totalVisitors,      // Sum of visitors from bookings
                'totalSatisfaction' => $totalSatisfaction,  // Count of positive feedback
                'lastSync'          => now()->format('M d, Y - H:i:s'),
                'serverStatus'      => 'Operational',
            ]
        ]);
    }
}