<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class VisitorController extends Controller
{
    /**
     * Display the main Dashboard
     */
    public function index()
    {
        return Inertia::render('Visitor/Dashboard', [
            'bookings' => Booking::where('user_id', auth()->id())
                ->where('status', '!=', 'completed')
                ->latest()
                ->get()
        ]);
    }

    /**
     * Display Booking History
     */
    public function history()
    {
        return Inertia::render('Visitor/History', [
            'bookings' => Booking::where('user_id', auth()->id())
                ->latest()
                ->get()
        ]);
    }

    /**
     * Show Personal Information Update Page
     */
    public function editProfile()
    {
        return Inertia::render('Visitor/Profile', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Show Security/Password Settings Page
     */
    public function securitySettings()
    {
        return Inertia::render('Visitor/Security');
    }

    /**
     * Handle Password Update Logic
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    // placeholder for other methods
    public function notifications() { return Inertia::render('Visitor/Notifications'); }
    public function createBooking() { return Inertia::render('Visitor/BookingForm'); }
}