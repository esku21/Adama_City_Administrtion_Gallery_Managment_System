<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageInteraction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * PUBLIC VIEW: For visitors.
     * Maps images to include 'is_liked' status for the current user's IP.
     */
    public function index(Request $request)
    {
        $ip = $request->ip();
        
        $images = Image::latest()->get()->map(function ($img) use ($ip) {
            // Check if this specific IP has already liked this image
            $img->is_liked = ImageInteraction::where('image_id', $img->id)
                ->where('ip_address', $ip)
                ->where('type', 'like')
                ->exists();
            return $img;
        });

        return Inertia::render('Gallery/index', [
            'images' => $images
        ]);
    }

    /**
     * ADMIN VIEW: For dashboard management.
     */
    public function adminIndex()
    {
        return Inertia::render('Admin/Gallery', [
            'images' => Image::latest()->get()
        ]);
    }

    /**
     * STORE: Handles the upload from the Admin Dashboard.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');

            Image::create([
                'title' => $request->title,
                'url' => Storage::url($path),
                'views_count' => 0,
                'likes_count' => 0,
                'dislikes_count' => 0,
            ]);
        }

        return back()->with('message', 'Image published successfully!');
    }

    /**
     * UPDATE: Edit title or replace the image file.
     */
    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $image->title = $request->title;

        if ($request->hasFile('image')) {
            // Delete old file
            $oldFilePath = str_replace('/storage/', '', $image->url);
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            // Store new file
            $newPath = $request->file('image')->store('gallery', 'public');
            $image->url = Storage::url($newPath);
        }

        $image->save();
        return back()->with('message', 'Image updated successfully!');
    }

    /**
     * DESTROY: Deletes the photo record and the physical file.
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $filePath = str_replace('/storage/', '', $image->url);
        
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $image->delete();
        return back()->with('message', 'Image deleted successfully!');
    }

    /**
     * INCREMENT VIEW: Only records one view per IP address.
     * FIXED: Returns back() instead of JSON to prevent the Inertia modal error.
     */
    public function incrementView(Request $request, $id) 
    {
        $ip = $request->ip();

        // Check if this IP already has a 'view' record for this image
        $exists = ImageInteraction::where('image_id', $id)
            ->where('ip_address', $ip)
            ->where('type', 'view')
            ->exists();

        if (!$exists) {
            ImageInteraction::create([
                'image_id' => $id,
                'ip_address' => $ip,
                'type' => 'view'
            ]);
            
            $image = Image::find($id);
            if ($image) {
                $image->increment('views_count');
            }
        }

        // Always return back() for Inertia requests
        return back();
    }

    /**
     * INCREMENT LIKE: Toggles likes (One per IP).
     * If already liked, clicking again will 'Unlike'.
     */
    public function incrementLike(Request $request, $id) 
    {
        $ip = $request->ip();
        $image = Image::findOrFail($id);

        // Check for existing like interaction
        $interaction = ImageInteraction::where('image_id', $id)
            ->where('ip_address', $ip)
            ->where('type', 'like')
            ->first();

        if (!$interaction) {
            // User hasn't liked it yet: Add Like record and increment count
            ImageInteraction::create([
                'image_id' => $id,
                'ip_address' => $ip,
                'type' => 'like'
            ]);
            $image->increment('likes_count');
        } else {
            // User already liked it: Toggle OFF (Delete record and decrement count)
            $interaction->delete();
            $image->decrement('likes_count');
        }

        // Use back() to refresh the data on the current page
        return back();
    }
}