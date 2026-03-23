<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide; 
use App\Models\Hall;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AdminGuideController extends Controller
{
    /**
     * Display the Guide management page.
     */
    public function index(): Response
    {
        // We load the hall relationship to show the assigned hall name in the table
        $guides = Guide::with('hall')->latest()->get();
        $halls = Hall::select('id', 'name')->get(); 

        return Inertia::render('Admin/Guides/Index', [
            'guides' => $guides,
            'halls' => $halls
        ]);
    }

    /**
     * Store a new guide and assign them the FIXED password: Adama@2026
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:guides,email',
            'phone'   => 'required|string|max:20',
            'gender'  => 'required|string|in:Male,Female',
            'hall_id' => 'required|exists:halls,id', 
        ]);

        $password = 'Adama@2026';

        Guide::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'gender'    => $validated['gender'],
            'hall_id'   => $validated['hall_id'],
            'password'  => Hash::make($password),
            'is_active' => true,
        ]);

        return redirect()->back()->with('flash', [
            'message' => "Guide {$validated['name']} assigned successfully! Default Password: " . $password,
            'type'    => 'success'
        ]);
    }

    /**
     * Update an existing guide's information.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $guide = Guide::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            // unique check excludes the current guide's ID to avoid conflict during update
            'email'   => 'required|string|email|max:255|unique:guides,email,' . $id,
            'phone'   => 'required|string|max:20',
            'gender'  => 'required|string|in:Male,Female',
            'hall_id' => 'required|exists:halls,id',
        ]);

        $guide->update($validated);

        return redirect()->back()->with('flash', [
            'message' => 'Guide updated successfully.',
            'type'    => 'success'
        ]);
    }

    /**
     * Remove a guide account.
     */
    public function destroy($id): RedirectResponse
    {
        $guide = Guide::findOrFail($id);
        $guide->delete();

        return redirect()->back()->with('flash', [
            'message' => 'Guide account removed successfully.',
            'type'    => 'info'
        ]);
    }
}