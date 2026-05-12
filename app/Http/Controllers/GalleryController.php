<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * PUBLIC VIEW: For visitors and guides
     */
    public function index()
    {
        return Inertia::render('Gallery/index', [
            'images' => Image::latest()->get()
        ]);
    }

    /**
     * ADMIN VIEW
     */
    public function adminIndex()
    {
        return Inertia::render('Admin/Gallery', [
            'images' => Image::latest()->get()
        ]);
    }

    /**
     * STORE: Handles the upload from the Admin Dashboard
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
            ]);
        }

        return back()->with('message', 'Image published successfully!');
    }

    /**
     * UPDATE: This fixes the "Call to undefined method" error
     */
    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Update Title
        $image->title = $request->title;

        // Update File if provided
        if ($request->hasFile('image')) {
            // 1. Delete old file from storage
            $oldFilePath = str_replace('/storage/', '', $image->url);
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            // 2. Store new file
            $newPath = $request->file('image')->store('gallery', 'public');
            $image->url = Storage::url($newPath);
        }

        $image->save();

        return back()->with('message', 'Image updated successfully!');
    }

    /**
     * DESTROY: Deletes the photo from DB and Storage
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        
        // Remove file from storage
        $filePath = str_replace('/storage/', '', $image->url);
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $image->delete();
        return back()->with('message', 'Image deleted successfully!');
    }

    /**
     * INTERACTIONS
     */
    public function incrementView(Request $request, $id) 
    {
        $image = Image::find($id);
        if ($image) { $image->increment('views_count'); }
        return back();
    }

    public function incrementLike(Request $request, $id) 
    {
        $image = Image::find($id);
        if ($image) { $image->increment('likes_count'); }
        return back();
    }
}