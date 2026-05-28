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
    public function index(): Response
    {
        // Eloquent automatically includes 'readable_slot' (from $appends in Model)
        $bookings = Booking::with(['halls', 'user'])->latest()->get();

        return Inertia::render('Admin/Bookings', [
            'bookings' => $bookings,
            'halls' => Hall::where('is_active', true)->get(), 
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name'       => 'required|string|max:255',
            'booking_date'       => 'required|date',
            'slot_id'            => 'required|string', // Added validation for slot
            'number_of_visitors' => 'required|integer|min:1',
            'status'             => 'required|string|in:pending,approved,confirmed,cancelled,rejected,completed,arrived,no-show',
            'hall_id'            => 'required|exists:halls,id',
        ]);

        try {
            $booking = Booking::create([
                'user_id'            => auth()->id(), 
                'visitor_name'       => $validated['visitor_name'],
                'booking_date'       => $validated['booking_date'],
                'slot_id'            => $validated['slot_id'], // Added slot_id
                'number_of_visitors' => $validated['number_of_visitors'],
                'status'             => strtolower($validated['status']),
                'visitor_category'   => 'Normal',
                'visitor_type'       => 'Manual',
                'qr_token'           => 'ACAGMS-ADM-' . strtoupper(Str::random(8)),
            ]);

            $booking->halls()->sync([$validated['hall_id']]);

            return back()->with('success', 'Manual booking authorized successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Store Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create booking registry.']);
        }
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['arrived', 'no-show'])) {
            return back()->withErrors(['error' => 'This booking status is finalized and cannot be modified.']);
        }

        $validated = $request->validate([
            'status'          => 'sometimes|required|string|in:pending,approved,confirmed,cancelled,rejected,completed,arrived,no-show',
            'reschedule_date' => 'nullable|date',
        ]);

        try {
            if (isset($validated['status'])) {
                $booking->status = strtolower($validated['status']);
            }

            $booking->save();

            if (isset($validated['status']) && $validated['status'] === 'rejected' && $request->reschedule_date) {
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

    public function destroy(Booking $booking): RedirectResponse
    {
        try {
            if ($booking->attachment && Storage::disk('public')->exists($booking->attachment)) {
                Storage::disk('public')->delete($booking->attachment);
            }
            $booking->halls()->detach();
            $booking->delete();
            return back()->with('success', 'Record and file purged successfully.');
        } catch (\Exception $e) {
            Log::error("Admin Delete Error: " . $e->getMessage());
            return back()->with('error', 'Deletion failed.');
        }
    }
}