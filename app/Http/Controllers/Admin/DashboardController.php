<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the main Admin Dashboard overview.
     */
    public function allBookings()
    {
        return Inertia::render('Admin/Dashboard', [
            'bookings' => Booking::with(['user', 'hall'])->latest()->take(10)->get(),
            'stats' => [
                'totalBookings'   => Booking::count(),
                'pendingBookings' => Booking::where('status', 'pending')->count(),
                'totalGuides'     => User::where('role', 'guide')->count(),
                'avgRating'       => Feedback::avg('rating') ?? 0,
            ]
        ]);
    }

    /**
     * Update user status (Active/Inactive)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => $request->is_active]);

        return redirect()->back()->with('message', 'Status updated successfully.');
    }

    /**
     * PROFESSIONAL REPORT GENERATOR LOGIC
     * Solves the MySQL "Alias in GroupBy" error by using DB::raw expressions.
     */
    public function reports(Request $request)
    {
        // 1. Initialize Query
        $query = Booking::query();

        // 2. Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 3. Define the SQL Expression for Grouping
        $groupBy = $request->input('group_by', 'day');
        
        if ($groupBy === 'month') {
            $expression = "DATE_FORMAT(created_at, '%Y-%m')";
        } elseif ($groupBy === 'year') {
            $expression = "YEAR(created_at)";
        } else {
            // Default: Group by Day
            $expression = "DATE(created_at)";
        }

        // 4. Determine Report Data based on Type
        $reportType = $request->input('report_type', 'bookings');
        
        if ($reportType === 'visitor_analysis') {
            // Sum of visitors per period
            $chartData = $query->selectRaw("$expression as label, SUM(number_of_visitors) as total")
                ->groupBy(DB::raw($expression))
                ->orderBy(DB::raw($expression), 'ASC')
                ->get();
        } else {
            // Default: Count of booking records per period
            $chartData = $query->selectRaw("$expression as label, COUNT(*) as total")
                ->groupBy(DB::raw($expression))
                ->orderBy(DB::raw($expression), 'ASC')
                ->get();
        }

        // 5. Summary Stats (Using clones to avoid modifying the main query)
        $summary = [
            'total_count'    => (clone $query)->count(),
            'total_visitors' => (clone $query)->sum('number_of_visitors'),
            'positive_feed'  => Feedback::where('rating', '>=', 4)->count(),
            'negative_feed'  => Feedback::where('rating', '<=', 2)->count(),
        ];

        return Inertia::render('Admin/Reports', [
            'reportData' => $chartData,
            'filters'    => $request->all(['report_type', 'start_date', 'end_date', 'group_by', 'chart_style']),
            'summary'    => $summary
        ]);
    }

    /**
     * View and Manage Feedback
     */
    public function viewFeedback()
    {
        return Inertia::render('Admin/Feedback', [
            'feedbacks' => Feedback::with('user')->latest()->get()
        ]);
    }
}