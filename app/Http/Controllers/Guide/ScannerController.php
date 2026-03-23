<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScannerController extends Controller
{
    public function index()
    {
        $guide = Auth::user(); // Assuming your Guide extends User or has a profile
        
        // Fetch bookings where the hall name matches the guide's assigned hall
        $bookings = Booking::whereHas('halls', function ($query) use ($guide) {
            $query->where('name', $guide->hallsnamemanage);
        })
        ->with('user')
        ->where('booking_date', '>=', now()->toDateString())
        ->orderBy('booking_date', 'asc')
        ->get();

        return Inertia::render('Guide/Dashboard', [
            'bookings' => $bookings,
            'managedHall' => $guide->hallsnamemanage
        ]);
    }
}