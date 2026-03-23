namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getMonthlyAttendance()
    {
        return Booking::select(
            DB::raw('count(booking_id) as count'),
            DB::raw("DATE_FORMAT(booking_date, '%M') as month")
        )
        ->groupBy('month')
        ->orderBy('booking_date')
        ->get();
    }
}