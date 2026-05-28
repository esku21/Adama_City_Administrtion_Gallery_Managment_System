<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $path = $request->user()->role === 'admin' ? 'Admin/Profile/Edit' : 'Visitor/Profile/Edit';
        
        return Inertia::render($path, [
            'status' => session('status'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validate fields
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName'  => 'required|string|max:255',
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_no'  => 'nullable|string|max:20',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->fill([
            'firstName' => $validated['firstName'],
            'lastName'  => $validated['lastName'],
            'email'     => $validated['email'],
            'phone_no'  => $validated['phone_no'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::back()->with('message', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Keep existing restriction if intended, or allow users to remove their own photo
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null; 
            $user->save();
        }

        return Redirect::back()->with('message', 'Photo removed.');
    }
}