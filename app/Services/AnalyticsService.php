<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get summary statistics (Excluding 'Admin' user)
     */
    public static function getSummaryData(): array
    {
        return [
            // Exclude users where name is 'Admin'
            'totalRegisteredUsers' => User::where('name', '!=', 'Admin')->count(),
            
            'totalBookings'        => Booking::count(),
            'totalVisitors'        => Booking::sum('number_of_visitors'),
            'totalSatisfied'       => Feedback::where('sentiment_status', 'LIKE', '%Positive%')->count(),
            'totalUnsatisfied'     => Feedback::where('sentiment_status', 'LIKE', '%Negative%')->count(),
        ];
    }

    /**
     * Get filtered chart data (Excluding 'Admin' user)
     */
    public static function getChartData(string $statusFilter): array
    {
        [$startDate, $endDate] = self::getDateRangeAndGroupBy($statusFilter);

        // Get aggregated totals for the selected time interval
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Exclude 'Admin' user from filtered count as well
        $totalUsers = User::whereBetween('created_at', [$startDate, $endDate])
                          ->where('name', '!=', 'Admin')
                          ->count();
                          
        $totalVisitors = Booking::whereBetween('created_at', [$startDate, $endDate])->sum('number_of_visitors');

        return [
            [
                'label' => 'Total Bookings',
                'bookings' => $totalBookings,
                'users' => 0,
                'visitors' => 0,
            ],
            [
                'label' => 'Registered Users',
                'bookings' => 0,
                'users' => $totalUsers,
                'visitors' => 0,
            ],
            [
                'label' => 'Total Visitors',
                'bookings' => 0,
                'users' => 0,
                'visitors' => $totalVisitors,
            ],
        ];
    }

    /**
     * Get date range based on filter
     */
    private static function getDateRangeAndGroupBy(string $statusFilter): array
    {
        $endDate = Carbon::now()->endOfDay();

        return match($statusFilter) {
            'weekly'  => [Carbon::now()->subDays(7)->startOfDay(), $endDate],
            'monthly' => [Carbon::now()->subDays(30)->startOfDay(), $endDate],
            'yearly'  => [Carbon::now()->subDays(365)->startOfDay(), $endDate],
            default   => [Carbon::now()->subDays(7)->startOfDay(), $endDate],
        };
    }
}