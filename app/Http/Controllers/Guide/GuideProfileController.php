<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class GuideProfileController extends Controller
{
    /**
     * Display the guide's profile.
     */
    public function edit(): Response
    {
        // Explicitly get the guide from the correct guard
        $guide = Auth::guard('guide')->user();
        
        // Ensure the hall relationship is loaded for the "Assigned Station" display
        $guide->load('hall');

        return Inertia::render('Guide/Profile', [
            'guide' => $guide,
            'hall' => $guide->hall
        ]);
    }

    /**
     * Update the guide's profile image.
     */
    public function updateImage(Request $request)
    {
        $request->validate([
            // 'image' matches the form.image key in your Profile.vue
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        /** @var \App\Models\Guide $guide */
        $guide = Auth::guard('guide')->user();

        if ($request->hasFile('image')) {
            // 1. Cleanup: Delete the old profile image if it exists to save server space
            if ($guide->profile_image && Storage::disk('public')->exists($guide->profile_image)) {
                Storage::disk('public')->delete($guide->profile_image);
            }

            // 2. Storage: Save the new file in public/profile_images
            $path = $request->file('image')->store('profile_images', 'public');
            
            // 3. Database: Update the guide record
            $guide->profile_image = $path;
            $guide->save();
        }

        // Return back with a flash message for the Inertia frontend
        return Redirect::back()->with('success', 'Profile identity updated successfully.');
    }
}