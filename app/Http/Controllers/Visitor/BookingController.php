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
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Dashboard
     */
    public function index(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Visitor/Dashboard', [
            'bookings' => Booking::with('halls')
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
        // Validate incoming multi-select arrays
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

        // CORE RULE: One user only books one visit bundle per day
        $alreadyBooked = Booking::where('user_id', $userId)
            ->whereDate('booking_date', $request->booking_date)
            ->whereIn('status', ['pending', 'approved', 'attended'])
            ->exists();

        if ($alreadyBooked) {
            return redirect()->back()
                ->with('error', "You already have an active booking on this date. Please modify your existing request or select another day and Time.")
                ->withInput();
        }

        $attachmentPath = null;

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Handle unique file upload once
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('bookings/attachments', 'public');
            }

            // Build structural full name
            $fullName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
            if (!$fullName) {
                $fullName = $user->name ?? 'Guest Visitor';
            }

            // Create EXACTLY ONE booking parent row
            $booking = Booking::create([
                'user_id' => $userId,
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

            // Global system backup fallback user ID just in case a hall doesn't have an assigned guide
            $globalFallbackGuideId = DB::table('users')->where('role', 'guide')->value('id') 
                ?? DB::table('users')->where('role', 'staff')->value('id') 
                ?? DB::table('users')->value('id');

            if (!$globalFallbackGuideId) {
                throw new \Exception("No user accounts found in the system to assign as a tour guide.");
            }

            // Map selections dynamically based on which guide owns which hall
            $pivotData = [];
            foreach ($request->hall_ids as $hallId) {
                
                // ✅ FIXED DYNAMIC LOOKUP: Swapped 'user_id' to 'id' to find the guide's ID assigned to the hall
                $actualGuideId = DB::table('halls')->where('id', $hallId)->value('id');

                $pivotData[] = [
                    'booking_id' => $booking->id,
                    'hall_id'    => $hallId,
                    'guide_id'   => $actualGuideId ?? $globalFallbackGuideId, // Uses the real guide, falls back if empty
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Perform a secure mass data injection into pivot table
            DB::table('booking_hall_guide')->insert($pivotData);

            DB::commit();

            return redirect()
                ->route('visitor.history')
                ->with('success', 'Booking bundle submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }

            Log::error("Booking Creation Error: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to save booking. Details: ' . $e->getMessage())
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
            'bookings' => Booking::with('halls')
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
        $booking = Booking::with('halls')->findOrFail($id);

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

            return Pdf::loadView('pdf.ticket', [
                'booking' => $booking,
                'qrCode' => $qrCodeBase64
            ])->setPaper('a4', 'portrait')
              ->download("Ticket_{$booking->qr_token}.pdf");

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