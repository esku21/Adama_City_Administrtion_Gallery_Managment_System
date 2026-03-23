<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Dashboard view with personal stats
     */
    public function index(): Response
    {
        $userId = Auth::id();
        
        $recentBookings = Booking::with('halls')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Visitor/Dashboard', [
            'bookings' => $recentBookings,
            'stats' => [
                'pendingVisits'   => Booking::where('user_id', $userId)->where('status', 'pending')->count(),
                'completedVisits' => Booking::where('user_id', $userId)->where('status', 'Approved')->count(),
                'totalBookings'   => Booking::where('user_id', $userId)->count(),
            ],
        ]);
    }

    /**
     * Show booking creation form
     */
    public function create(): Response
    {
        // Get active halls to populate the selection step
        $halls = Hall::where('is_active', true)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Visitor/BookingCreate', [
            'halls' => $halls
        ]);
    }

    /**
     * Store a new booking in the database
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'hall_ids'           => 'required|array|min:1',
            'hall_ids.*'         => 'exists:halls,id',
            'visitor_category'   => 'required|in:VIP,Normal',
            'visitor_type'       => 'required|string',
            'organization_name'  => 'nullable|string|max:255',
            'number_of_visitors' => 'required|integer|min:1',
            'booking_date'       => 'required|date|after_or_equal:today',
            'slot_id'            => 'required|string', 
            'attachment'         => $request->visitor_category === 'VIP' 
                                    ? 'required|file|mimes:pdf,jpg,png,docx|max:5120' 
                                    : 'nullable|file|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $attachmentPath = null;

            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('bookings/attachments', 'public');
            }

            // Logic to determine name: check profile fields first
            $fullName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
            if (empty($fullName)) {
                $fullName = $user->name ?? 'Guest User';
            }

            // CREATE BOOKING
            // Note: qr_token is handled automatically by the Booking Model boot method
            $booking = Booking::create([
                'user_id'            => $user->id,
                'hall_id'            => $request->hall_ids[0], 
                'visitor_name'       => $fullName,
                'visitor_category'   => $request->visitor_category,
                'visitor_type'       => $request->visitor_type,
                'organization_name'  => $request->organization_name,
                'number_of_visitors' => $request->number_of_visitors,
                'booking_date'       => $request->booking_date,
                'slot_id'            => $request->slot_id, 
                'status'             => 'pending',
                'attachment_path'    => $attachmentPath,
            ]);

            // Sync many-to-many halls
            $booking->halls()->sync($request->hall_ids);

            DB::commit();

            return redirect()->route('visitor.history')->with('success', 'Visit booked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Booking Creation Error: " . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while saving your booking.'])
                ->withInput();
        }
    }

    /**
     * Display full history of user activity
     */
    public function history(): Response
    {
        $userId = Auth::id();
        
        return Inertia::render('Visitor/History', [
            'bookings'  => Booking::with('halls')->where('user_id', $userId)->latest()->get(),
            'feedbacks' => Feedback::where('user_id', $userId)->latest()->get()
        ]);
    }

    /**
     * Generate and download PDF Ticket
     */
    public function downloadTicket(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load('halls');

        // Generate QR Code
        $qrCode = base64_encode(QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($booking->qr_token));

        $pdf = Pdf::loadView('pdf.ticket', [
            'booking' => $booking,
            'qrCode'  => $qrCode
        ]);

        return $pdf->download('ACAGMS_Ticket_' . $booking->id . '.pdf');
    }

    /**
     * Cancel a booking
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        try {
            if ($booking->attachment_path) {
                Storage::disk('public')->delete($booking->attachment_path);
            }

            $booking->halls()->detach(); 
            $booking->delete();

            return redirect()->back()->with('success', 'Booking has been cancelled.');
        } catch (\Exception $e) {
            Log::error("Booking Deletion Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Could not cancel booking.');
        }
    }
}