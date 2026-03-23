<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form.
     */
    public function create()
    {
        $halls = Hall::select('id', 'name')->get();

        return Inertia::render('Visitor/Feedback/Create', [
            'halls' => $halls
        ]);
    }

    /**
     * Store the feedback in the database.
     */
    public function store(Request $request)
    {
        // 1. DUPLICATE CHECK
        if ($request->booking_id) {
            $hasSubmitted = Feedback::where('booking_id', $request->booking_id)->exists();
            if ($hasSubmitted) {
                return back()->withErrors([
                    'message' => 'You have already submitted feedback for this specific visit.'
                ]);
            }
        }

        // 2. STRICT VALIDATION 
        // We use two regex rules: one for vowels and one for letters to catch "dfbcds" or "12345"
        $validated = $request->validate([
            'type'       => 'required|in:general,hall',
            'subject'    => 'required|string|max:255',
            'message'    => [
                'required',
                'string',
                'min:10',
                'max:2000',
                'regex:/[aeiouAEIOU]/', // Prevents "bcdfgh"
                'regex:/[a-zA-Z]/',      // Prevents "123456"
            ],
            'rating'     => 'required|integer|min:1|max:5',
            'hall_id'    => 'required_if:type,hall|nullable|exists:halls,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            // THE EXACT RESPONSE YOU REQUESTED:
            'message.regex'    => 'Please insert correct text feedback.',
            'message.min'      => 'Your comment is too short. Please provide at least 10 characters.',
            'message.required' => 'The feedback message is required.',
        ]);

        // 3. IMAGE HANDLING
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('feedback_images', 'public');
        }

        // 4. DATABASE CREATION
        Feedback::create([
            'user_id'    => Auth::id(),
            'booking_id' => $request->booking_id,
            'type'       => $validated['type'],
            'hall_id'    => ($validated['type'] === 'hall') ? $validated['hall_id'] : null,
            'subject'    => $validated['subject'],
            'message'    => strip_tags($validated['message']), 
            'rating'     => $validated['rating'],
            'image_path' => $imagePath,
        ]);

        // 5. SUCCESS RESPONSE
        return redirect()->route('visitor.history')
            ->with('success', 'Thank you! Your feedback has been successfully submitted.');
    }
}