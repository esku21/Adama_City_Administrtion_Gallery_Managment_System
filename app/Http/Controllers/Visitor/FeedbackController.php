<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Hall;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    /**
     * Show the form for creating a new feedback entry.
     */
    public function create()
    {
        $userId = Auth::id();

        // 1. Get halls where the user has actually arrived ('arrived' status)
        // 2. Exclude halls where they already submitted feedback
        // ✅ FIXED: Using relational mapping without running into missing column bugs
        $halls = Hall::where('is_active', true)
            ->whereHas('bookings', function ($query) use ($userId) {
                $query->where('bookings.user_id', $userId)
                      ->where('bookings.status', 'arrived');
            })
            ->whereDoesntHave('feedbacks', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->select('id', 'name')
            ->get();

        return Inertia::render('Visitor/Feedback/Create', [
            'halls' => $halls
        ]);
    }

    /**
     * Store a newly created feedback entry in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        // Server-side guard: Verify user has an arrived booking for this hall and no existing feedback
        if ($request->type === 'hall') {
            $hallId = $request->hall_id;

            // ✅ FIXED: Evaluates pivot table mapping records safely 
            $hasValidBooking = Booking::where('user_id', $userId)
                ->where('status', 'arrived')
                ->whereHas('halls', function ($query) use ($hallId) {
                    $query->where('halls.id', $hallId);
                })
                ->exists();

            if (!$hasValidBooking) {
                return back()->withErrors([
                    'hall_id' => 'Please first visit this hall before sending feedback.'
                ]);
            }

            $alreadySubmitted = Feedback::where('user_id', $userId)
                ->where('hall_id', $hallId)
                ->exists();

            if ($alreadySubmitted) {
                return back()->withErrors([
                    'hall_id' => 'You have already submitted feedback for this hall visit.'
                ]);
            }
        }

        $validated = $request->validate([
            'type'       => 'required|in:general,hall',
            'subject'    => 'required|string|max:255',
            'message'    => [
                'required', 'string', 'min:10', 'max:2000',
                'regex:/[aeiouAEIOU]/', 'regex:/[a-zA-Z]/', 'regex:/\s/',
                'not_regex:/(.)\1{3,}/', 'not_regex:/[^aeiou\s]{6,}/i',
            ],
            'rating'     => 'required|integer|min:1|max:5',
            'hall_id'    => 'required_if:type,hall|nullable|exists:halls,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'images'     => 'nullable|array|max:3',
            'images.*'   => 'image|mimes:jpg,jpeg,png,webp|max:3072', 
        ]);

        $sentimentStatus = 'Neutral';
        if ($validated['rating'] >= 4) {
            $sentimentStatus = 'Satisfaction';
        } elseif ($validated['rating'] <= 2) {
            $sentimentStatus = 'Unsatisfactory';
        }

        $messageLower = strtolower($validated['message']);
        $topicTag = 'General';
        if (preg_match('/(staff|guide|worker|person|service|security|help)/i', $messageLower)) {
            $topicTag = 'Staff';
        } elseif (preg_match('/(hot|light|ac|clean|room|hall|building|facility|toilet|bathroom|ventilation)/i', $messageLower)) {
            $topicTag = 'Facilities';
        }

        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('feedback_images', 'public');
            }
        }

        Feedback::create([
            'user_id'          => $userId,
            'booking_id'       => $request->booking_id,
            'type'             => $validated['type'],
            'hall_id'          => ($validated['type'] === 'hall') ? $validated['hall_id'] : null,
            'subject'          => $validated['subject'],
            'message'          => strip_tags($validated['message']), 
            'rating'           => $validated['rating'],
            'sentiment_status' => $sentimentStatus,
            'topic_tag'        => $topicTag,
            'image_path'       => !empty($paths) ? json_encode($paths) : null,
        ]);

        return redirect()->route('visitor.history')
            ->with('success', 'Thank you! Your feedback has been submitted as ' . $sentimentStatus);
    }
}