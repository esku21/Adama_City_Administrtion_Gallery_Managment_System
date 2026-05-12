<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class GuideController extends Controller
{
    /**
     * Display the Guide Dashboard with Hall-specific stats.
     */
    public function index(Request $request)
    {
        $guide = Auth::guard('guide')->user();
        
        if (!$guide) {
            return redirect()->route('guide.login');
        }

        $today = Carbon::today()->toDateString();
        $selectedDate = $request->input('date', $today);

        $timeMapping = [
            'm1' => '09:00 AM - 09:30 AM',
            'm2' => '10:00 AM - 10:30 AM',
            'm3' => '11:00 AM - 11:30 AM',
            'a1' => '02:00 PM - 02:30 PM',
            'a2' => '03:00 PM - 03:30 PM',
            'a3' => '04:00 PM - 04:30 PM',
        ];

        // Base Query: Restricted to this guide's assigned hall
        $hallBookings = Booking::where('hall_id', $guide->hall_id);

        $stats = [
            'total_bookings' => (clone $hallBookings)->count(),
            'pending_today'  => (clone $hallBookings)
                                ->whereDate('booking_date', $selectedDate)
                                ->whereIn('status', ['pending', 'approved', 'Approved'])
                                ->count(),
            'arrived_today'  => (clone $hallBookings)
                                ->whereDate('booking_date', $selectedDate)
                                ->where('status', 'Arrived')
                                ->count(),
        ];

        $bookings = (clone $hallBookings)
            ->with(['hall']) 
            ->whereDate('booking_date', '>=', $today) 
            ->orderBy('booking_date', 'asc')
            ->get()
            ->map(function ($booking) use ($timeMapping) {
                return [
                    'id'            => $booking->id,
                    'visitor_name'  => $booking->visitor_name ?? 'Unnamed Visitor',
                    'visitor_type'  => $booking->visitor_type,
                    'status'        => $booking->status ?? 'pending',
                    'booking_date'  => $booking->booking_date instanceof Carbon 
                                        ? $booking->booking_date->toDateString() 
                                        : $booking->booking_date,
                    'hall_names'    => $booking->hall->name ?? 'Station Hall', 
                    'readable_slot' => $timeMapping[strtolower($booking->slot_id)] ?? ($booking->slot_id ?? 'N/A'), 
                ];
            });

        return Inertia::render('Guide/Dashboard', [
            'bookings' => $bookings,
            'stats'    => $stats,
            'hallName' => $guide->hall->name ?? ('Hall #' . $guide->hall_id),
            'filters'  => ['date' => $selectedDate]
        ]);
    }

    /**
     * MISSING METHOD FIXED: Opens the QR Scanner View
     */
    public function scanner()
    {
        $guide = Auth::guard('guide')->user();
        return Inertia::render('Guide/Scanner', [
            'hallName' => $guide->hall->name ?? 'Station'
        ]);
    }

    /**
     * Updates visitor status via dashboard buttons
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);

        try {
            $guide = Auth::guard('guide')->user();
            $booking = Booking::where('hall_id', $guide->hall_id)->findOrFail($id);
            
            $statusMapping = [
                'Arrived' => 'Arrived',
                'Late'    => 'Off-Schedule',
                'Missed'  => 'No-Show'
            ];
            
            $newStatus = $statusMapping[$request->status] ?? $request->status;

            $booking->update([
                'status' => $newStatus,
                'attended_at' => now(), 
            ]);

            return back()->with('success', "Visitor marked as $newStatus");
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }
}