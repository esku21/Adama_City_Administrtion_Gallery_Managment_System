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
     * Dashboard
     */
    public function index(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Visitor/Dashboard', [
            'bookings' => Booking::with('hall')
                ->where('user_id', $userId)
                ->latest()
                ->limit(5)
                ->get(),
            'stats' => [
                'pendingVisits' => Booking::where('user_id', $userId)->where('status', 'pending')->count(),
                'completedVisits' => Booking::where('user_id', $userId)->where('status', 'approved')->count(),
                'totalBookings' => Booking::where('user_id', $userId)->count(),
            ],
        ]);
    }

    /**
     * Create form
     */
    public function create(): Response
    {
        return Inertia::render('Visitor/BookingCreate', [
            'halls' => Hall::where('is_active', true)
                ->select('id', 'name', 'location')
                ->get()
        ]);
    }

    /**
     * Store booking
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ VALIDATION
        $request->validate([
            'hall_ids' => 'required|array|min:1',
            'hall_ids.*' => 'exists:halls,id',
            'visitor_category' => 'required|in:VIP,Normal',
            'visitor_type' => 'required|string',
            'organization_name' => 'nullable|string|max:255',
            'number_of_visitors' => 'required|integer|min:1|max:50',
            'booking_date' => 'required|date|after_or_equal:today',
            'slot_id' => 'required|string',
            'attachment' => $request->visitor_category === 'VIP'
                ? 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
                : 'nullable|file|max:5120',
        ]);

        $userId = Auth::id();

        // ❌ STRICT RULE: only ONE booking per user per date
        $existingBooking = Booking::where('user_id', $userId)
            ->whereDate('booking_date', $request->booking_date)
            ->whereIn('status', ['pending', 'approved', 'attended'])
            ->first();

        if ($existingBooking) {
            return redirect()->back()
                ->with('error', 'You already have a booking on this date. Please choose another date & time.')
                ->withInput();
        }

        $attachmentPath = null;

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // file upload
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('bookings/attachments', 'public');
            }

            // name build
            $fullName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
            if (!$fullName) {
                $fullName = $user->name ?? 'Guest Visitor';
            }

            // create booking
            Booking::create([
                'user_id' => $userId,
                'hall_id' => $request->hall_ids[0],
                'visitor_name' => $fullName,
                'visitor_category' => $request->visitor_category,
                'visitor_type' => $request->visitor_type,
                'organization_name' => $request->organization_name,
                'number_of_visitors' => $request->number_of_visitors,
                'booking_date' => $request->booking_date,
                'slot_id' => strtolower($request->slot_id),
                'status' => 'pending',
                'attachment' => $attachmentPath,
                'qr_token' => 'ACAGMS-' . strtoupper(bin2hex(random_bytes(4))),
            ]);

            DB::commit();

            return redirect()
                ->route('visitor.history')
                ->with('success', 'Booking submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }

            Log::error("Booking Creation Error: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to save booking. Please try again.')
                ->withInput();
        }
    }

    /**
     * History
     */
    public function history(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Visitor/History', [
            'bookings' => Booking::with('hall')
                ->where('user_id', $userId)
                ->latest()
                ->get(),
            'feedbacks' => Feedback::where('user_id', $userId)
                ->latest()
                ->get()
        ]);
    }

    /**
     * Download ticket
     */
    public function downloadTicket($id)
    {
        $booking = Booking::with('hall')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $qrCodeData = QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($booking->qr_token);

            $qrCodeBase64 = base64_encode($qrCodeData);

            $pdf = Pdf::loadView('pdf.ticket', [
                'booking' => $booking,
                'qrCode' => $qrCodeBase64
            ])->setPaper('a4', 'portrait');

            return $pdf->download("Ticket_{$booking->qr_token}.pdf");

        } catch (\Exception $e) {
            Log::error("Ticket Error: " . $e->getMessage());
            return back()->with('error', 'Could not generate ticket.');
        }
    }

    /**
     * Delete booking
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        try {
            if ($booking->attachment) {
                Storage::disk('public')->delete($booking->attachment);
            }

            $booking->delete();

            return back()->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            Log::error("Delete Error: " . $e->getMessage());

            return back()->with('error', 'Deletion failed.');
        }
    }
}