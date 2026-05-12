<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback; // Ensure this is imported to fetch feedback data
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    /**
     * Display the system reports and analytics page.
     * This method pulls live data for users and feedback satisfaction.
     */
    public function index()
    {
        // 1. Fetch the total count of registered users
        $totalUsers = User::count();

        // 2. Fetch users who registered within the last 24 hours
        $newUsersToday = User::where('created_at', '>=', now()->subDay())->count();

        /**
         * 3. Fetch Satisfaction count from Feedback table.
         * This counts entries where the sentiment is exactly 'Satisfaction'.
         */
        $totalSatisfaction = Feedback::where('sentiment_status', 'Satisfaction')->count();

        /**
         * 4. Render the Inertia page.
         * Path: resources/js/Pages/Reports/Index.vue
         */
        return Inertia::render('Reports/Index', [
            'stats' => [
                'totalUsers'        => $totalUsers,
                'newUsersToday'     => $newUsersToday,
                'totalSatisfaction' => $totalSatisfaction, // Sent to the Vue frontend
                'lastSync'          => now()->format('M d, Y - H:i:s'),
                'serverStatus'      => 'Operational',
            ]
        ]);
    }
}