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
    public function index(Request $request)
    {
        $guide = Auth::guard('guide')->user();
        
        if (!$guide) {
            return redirect()->route('guide.login');
        }

        // Current System Date (2026-03-23)
        $today = Carbon::today()->toDateString();
        $selectedDate = $request->input('date', $today);

        // Main Query: Filter by the Hall ID assigned to the Guide
        // IMPORTANT: Ensure your 'booking_hall' pivot table has these links!
        $bookingsQuery = Booking::whereHas('halls', function ($query) use ($guide) {
            $query->where('halls.id', $guide->hall_id);
        });

        // 1. Stats Calculation (Strictly for today/selected date)
        $stats = [
            'total_bookings' => (clone $bookingsQuery)->count(),
            
            'pending_today'  => (clone $bookingsQuery)
                                ->whereDate('booking_date', $selectedDate)
                                ->whereIn('status', ['pending', 'Approved', 'approved'])
                                ->count(),
                                    
            'arrived_today'  => (clone $bookingsQuery)
                                ->whereDate('booking_date', $selectedDate)
                                ->whereIn('status', ['Arrived', 'arrived'])
                                ->count(),
        ];

        // 2. Table Data Fetching
        // We widen the range to Yesterday -> Tomorrow so your test data shows up
        $bookings = (clone $bookingsQuery)
            ->with(['halls']) 
            ->whereBetween('booking_date', [
                Carbon::yesterday()->toDateString(), 
                Carbon::tomorrow()->toDateString()
            ])
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id'            => $booking->id,
                    'visitor_name'  => $booking->visitor_name ?? 'Unnamed Visitor',
                    'visitor_type'  => $booking->visitor_type,
                    'status'        => $booking->status ?? 'pending',
                    // These use the getHallNamesAttribute and getReadableSlotAttribute from your Model
                    'hall_names'    => $booking->hall_names,
                    'readable_slot' => $booking->readable_slot,
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
     * Updates status from the Dashboard buttons
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Arrived,Off-Schedule,No-Show,Late,Missed'
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            // Map frontend button labels to database status values
            $statusMapping = [
                'Late'   => 'Off-Schedule',
                'Missed' => 'No-Show'
            ];
            
            $newStatus = $statusMapping[$request->status] ?? $request->status;

            $booking->update(['status' => $newStatus]);

            return back()->with('message', 'Status updated to ' . $newStatus);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Could not update status.']);
        }
    }
}