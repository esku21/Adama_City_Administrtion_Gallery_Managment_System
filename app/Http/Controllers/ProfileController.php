<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Renders different views based on role
        $path = $request->user()->role === 'admin' ? 'Admin/Profile/Edit' : 'Visitor/Profile/Edit';
        
        return Inertia::render($path, [
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     * Restricted: Only Admins can update profile info.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // BLOCK VISITORS: Prevent updates if the user is not an admin
        if ($user->role !== 'admin') {
            return Redirect::back()->withErrors([
                'auth' => 'Registration confirmed. Profile updates are restricted for visitor accounts.'
            ]);
        }

        // Validate all fields present in your Vue form
        $rules = [
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_no'  => 'nullable|string|max:20',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $validated = $request->validate($rules);

        // Update basic information
        $user->fill([
            'firstName' => $validated['firstName'],
            'lastName'  => $validated['lastName'],
            'email'     => $validated['email'],
            'phone_no'  => $validated['phone_no'],
        ]);

        // Handle Profile Photo Upload
        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        // Reset email verification if email was changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::back()->with('message', 'Profile updated successfully.');
    }

    /**
     * Delete the user's profile photo.
     * Restricted: Only Admins can manage photos.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null; 
            $user->save();
        }

        return Redirect::back()->with('message', 'Photo removed.');
    }
}