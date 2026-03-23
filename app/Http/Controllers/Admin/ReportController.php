<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'weekly'); 
        
        $startDate = match ($type) {
            'monthly' => Carbon::now()->startOfMonth(),
            'yearly'  => Carbon::now()->startOfYear(),
            default   => Carbon::now()->startOfWeek(),
        };

        // 1. STATS: Using your 'number_of_visitors' column
        $totalPeople = Booking::where('status', 'approved')
            ->where('booking_date', '>=', $startDate)
            ->sum('number_of_visitors');

        $totalBookings = Booking::where('status', 'approved')
            ->where('booking_date', '>=', $startDate)
            ->count();

        $avgGroup = $totalBookings > 0 ? round($totalPeople / $totalBookings, 1) : 0;

        // 2. FEEDBACK: (Ensure your Feedback table has a 'rating' column)
        $feedbacks = Feedback::where('created_at', '>=', $startDate)->get();
        $totalF = $feedbacks->count();
        $goodF = $feedbacks->where('rating', '>=', 4)->count();
        $badF = $feedbacks->where('rating', '<=', 2)->count();

        // 3. TREND DATA: Using 'booking_date' instead of created_at for accuracy
        $reportData = Booking::where('status', 'approved')
            ->where('booking_date', '>=', $startDate)
            ->select(
                'booking_date as period',
                DB::raw('COUNT(*) as total_visits'),
                DB::raw('SUM(number_of_visitors) as total_people')
            )
            ->groupBy('booking_date')
            ->orderBy('booking_date', 'ASC')
            ->get();

        // 4. VISITOR TYPES: Using your 'visitor_type' column
        $visitorTypes = Booking::where('status', 'approved')
            ->where('booking_date', '>=', $startDate)
            ->select('visitor_type', DB::raw('count(*) as count'))
            ->groupBy('visitor_type')
            ->pluck('count', 'visitor_type')
            ->toArray();

        return Inertia::render('Admin/Reports', [
            'reportData' => $reportData,
            'currentType' => $type,
            'summary' => [
                'total_people_served' => $totalPeople,
                'avg_group_size' => $avgGroup,
                'good_feedback' => $totalF > 0 ? round(($goodF / $totalF) * 100) : 0,
                'bad_feedback' => $totalF > 0 ? round(($badF / $totalF) * 100) : 0,
                'visitor_types' => $visitorTypes ?: ['General' => 0],
            ]
        ]);
    }
}