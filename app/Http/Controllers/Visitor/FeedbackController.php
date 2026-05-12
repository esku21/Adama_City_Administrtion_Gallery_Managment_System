<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    public function create()
    {
        $halls = Hall::where('is_active', true)->select('id', 'name')->get();

        return Inertia::render('Visitor/Feedback/Create', [
            'halls' => $halls
        ]);
    }

    public function store(Request $request)
    {
        if ($request->booking_id) {
            $hasSubmitted = Feedback::where('booking_id', $request->booking_id)->exists();
            if ($hasSubmitted) {
                return back()->withErrors([
                    'message' => 'You have already submitted feedback for this specific visit.'
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
            // Updated validation for array of images
            'images'     => 'nullable|array|max:3',
            'images.*'   => 'image|mimes:jpg,jpeg,png,webp|max:3072', 
        ]);

        // Logic for Sentiment
        $sentimentStatus = 'Neutral';
        if ($validated['rating'] >= 4) {
            $sentimentStatus = 'Satisfaction';
        } elseif ($validated['rating'] <= 2) {
            $sentimentStatus = 'Unsatisfactory';
        }

        // Topic Tagging
        $messageLower = strtolower($validated['message']);
        $topicTag = 'General';
        if (preg_match('/(staff|guide|worker|person|service|security|help)/i', $messageLower)) {
            $topicTag = 'Staff';
        } elseif (preg_match('/(hot|light|ac|clean|room|hall|building|facility|toilet|bathroom|ventilation)/i', $messageLower)) {
            $topicTag = 'Facilities';
        }

        // Handle Multiple Image Uploads
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('feedback_images', 'public');
            }
        }

        Feedback::create([
            'user_id'          => Auth::id(),
            'booking_id'       => $request->booking_id,
            'type'             => $validated['type'],
            'hall_id'          => ($validated['type'] === 'hall') ? $validated['hall_id'] : null,
            'subject'          => $validated['subject'],
            'message'          => strip_tags($validated['message']), 
            'rating'           => $validated['rating'],
            'sentiment_status' => $sentimentStatus,
            'topic_tag'        => $topicTag,
            // Store as JSON if your DB column is TEXT or JSON, 
            // or just store the first one if the DB only supports one string.
            'image_path'       => !empty($paths) ? json_encode($paths) : null,
        ]);

        return redirect()->route('visitor.history')
            ->with('success', 'Thank you! Your feedback has been submitted as ' . $sentimentStatus);
    }
}