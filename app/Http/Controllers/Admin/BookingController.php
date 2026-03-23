<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Display the Admin Registry
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Bookings', [
            'bookings' => Booking::with('halls')->latest()->get(),
        ]);
    }

    /**
     * Store a manually created booking (Admin Entry)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name'       => 'required|string|max:255',
            'booking_date'       => 'required|date',
            'number_of_visitors' => 'required|integer|min:1',
            'status'             => 'required|string|in:pending,Approved,confirmed,cancelled,rejected',
        ]);

        try {
            Booking::create(array_merge($validated, [
                'user_id'          => auth()->id(), // Admin is the creator
                'visitor_category' => 'Normal',     // Default for manual entry
                'visitor_type'     => 'Manual',
                'qr_token'         => (string) Str::uuid(),
            ]));

            return back()->with('success', 'Manual booking authorized.');
        } catch (\Exception $e) {
            Log::error("Admin Store Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create booking.']);
        }
    }

    /**
     * Update an existing booking record
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name'       => 'required|string|max:255',
            'booking_date'       => 'required|date',
            'number_of_visitors' => 'required|integer|min:1',
            'status'             => 'required|string|in:pending,Approved,confirmed,cancelled,rejected',
        ]);

        try {
            $booking->update($validated);
            return back()->with('success', 'Registry updated successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Update Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Update failed.']);
        }
    }

    /**
     * Quick Patch for Status dropdown
     */
    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate(['status' => 'required|string']);
        
        $booking->update(['status' => $request->status]);
        
        return back()->with('success', 'Status synced to: ' . $request->status);
    }

    /**
     * Permanent removal of a booking
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Delete associated file if it exists
            if ($booking->attachment_path) {
                Storage::disk('public')->delete($booking->attachment_path);
            }

            // Detach many-to-many relationships (halls)
            $booking->halls()->detach();

            // Delete the main record
            $booking->delete();

            DB::commit();
            return back()->with('success', 'Record purged from registry.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Admin Delete Error: " . $e->getMessage());
            return back()->with('error', 'Critical failure during deletion.');
        }
    }
}