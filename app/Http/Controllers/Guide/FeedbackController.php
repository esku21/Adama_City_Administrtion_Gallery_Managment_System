<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback specifically for the Guide's assigned Hall.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // 1. Get the hall assigned to this Guide
        // 2. Fetch only feedback belonging to that hall
        // 3. Eager load the 'user' (visitor) who wrote the feedback
        $feedbacks = Feedback::with(['user'])
            ->where('hall_id', $user->hall_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Guide/Feedbacks/Index', [
            'feedbacks' => $feedbacks,
            'hallName'  => $user->hall?->name ?? 'Your Assigned Hall',
        ]);
    }

    /**
     * Remove the specified feedback from the Guide's hall.
     * Includes a security check to ensure the Guide owns this hall.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        
        // Find the feedback only if it belongs to the Guide's hall
        $feedback = Feedback::where('id', $id)
            ->where('hall_id', $user->hall_id)
            ->firstOrFail();

        $feedback->delete();

        return redirect()->back()
            ->with('success', 'Feedback has been successfully removed from your list.');
    }
}