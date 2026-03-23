<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    /**
     * Display the feedback list with user relationship.
     */
    public function index(): Response
    {
        // 'with(user)' ensures we get the visitor's name/email from the users table
        // 'orderBy(created_at)' matches your migration's custom timestamp column
        $feedbacks = Feedback::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Feedbacks/Index', [
            'feedbacks' => $feedbacks
        ]);
    }

    /**
     * Delete a feedback record.
     */
    public function destroy($id): RedirectResponse
    {
        // Using $id directly ensures no Route-Model binding conflicts 
        // with singular table names
        $feedback = Feedback::findOrFail($id);
        
        $feedback->delete();

        return redirect()->back()->with('success', 'Feedback deleted successfully');
    }
}