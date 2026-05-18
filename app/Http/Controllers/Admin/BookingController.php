<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Display the Admin Registry with functional document links
     */
    public function index(): Response
    {
        // ✅ FIXED: Changed singular 'hall' relation to plural 'halls' to match your model relationship
        $bookings = Booking::with(['halls', 'user'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                // Generates the public URL for the frontend.
                $booking->attachment_url = ($booking->attachment && Storage::disk('public')->exists($booking->attachment)) 
                    ? asset('storage/' . $booking->attachment) 
                    : null;
                
                // ✅ FIXED: Append a safe fallback 'hall_id' string or object value for your Vue components
                $booking->hall_id = $booking->halls->first()->id ?? null;
                
                return $booking;
            });

        return Inertia::render('Admin/Bookings', [
            'bookings' => $bookings,
            'halls' => Hall::where('is_active', true)->get(), 
        ]);
    }

    /**
     * Store a manually created booking
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name'       => 'required|string|max:255',
            'booking_date'       => 'required|date',
            'number_of_visitors' => 'required|integer|min:1',
            'status'             => 'required|string|in:pending,approved,confirmed,cancelled,rejected,extended,completed',
            'hall_id'            => 'required|exists:halls,id',
        ]);

        try {
            // ✅ FIXED: Remove hall_id from the direct Booking field creation mapping
            $booking = Booking::create([
                'user_id'            => auth()->id(), 
                'visitor_name'       => $validated['visitor_name'],
                'booking_date'       => $validated['booking_date'],
                'number_of_visitors' => $validated['number_of_visitors'],
                'status'             => strtolower($validated['status']),
                'visitor_category'   => 'Normal',
                'visitor_type'       => 'Manual',
                'qr_token'           => 'ACAGMS-ADM-' . strtoupper(Str::random(8)),
            ]);

            // ✅ FIXED: Attach the selected hall to the many-to-many pivot table layout
            $booking->halls()->sync([$validated['hall_id']]);

            return back()->with('success', 'Manual booking authorized successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Store Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create booking registry.']);
        }
    }

    /**
     * Update existing booking record
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name'       => 'required|string|max:255',
            'booking_date'       => 'required|date',
            'number_of_visitors' => 'required|integer|min:1',
            'status'             => 'required|string|in:pending,approved,confirmed,cancelled,rejected,extended,completed',
            'hall_id'            => 'required|exists:halls,id',
            'reschedule_date'    => 'nullable|date',
        ]);

        try {
            // ✅ FIXED: Isolate hall_id so it does not update straight into the bookings column structure
            $hallId = $validated['hall_id'];
            unset($validated['hall_id']);

            // Update local booking data fields safely
            $booking->update($validated);

            // ✅ FIXED: Sync the relationship updates cleanly over the pivot bridge
            $booking->halls()->sync([$hallId]);

            // Handle rejection announcements if a reschedule date is provided
            if ($booking->status === 'rejected' && $request->reschedule_date) {
                Announcement::create([
                    'user_id'         => $booking->user_id,
                    'title'           => 'Booking Reschedule Offered',
                    'content'         => "Your booking for {$booking->visitor_name} was rejected. A new slot is available on {$request->reschedule_date}.",
                    'type'            => 'warning',
                    'reschedule_date' => $request->reschedule_date,
                ]);
            }

            return back()->with('success', 'Registry updated successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Update Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete record and associated file from storage
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        try {
            // Check for file in the 'public' disk and delete it to save space
            if ($booking->attachment && Storage::disk('public')->exists($booking->attachment)) {
                Storage::disk('public')->delete($booking->attachment);
            }

            // ✅ FIXED: Safely sever pivot references before execution to avoid dangling database table items
            $booking->halls()->detach();

            $booking->delete();
            return back()->with('success', 'Record and file purged successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Delete Error: " . $e->getMessage());
            return back()->with('error', 'Deletion failed.');
        }
    }
}