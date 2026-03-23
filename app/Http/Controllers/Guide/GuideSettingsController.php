<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GuideSettingsController extends Controller
{
    /**
     * Display the guide's settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('Guide/Settings');
    }

    /**
     * Update the guide's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:guide'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Update the password
        $request->user('guide')->update([
            'password' => Hash::make($validated['password']),
        ]);

        /**
         * We return both 'message' and 'flash' to ensure compatibility with 
         * different HandleInertiaRequests middleware setups.
         */
        return back()->with([
            'message' => 'Password updated successfully.',
            'flash' => [
                'message' => 'Password updated successfully.',
                'type' => 'success'
            ]
        ]);
    }
}