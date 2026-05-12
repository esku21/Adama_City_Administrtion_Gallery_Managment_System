<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * PUBLIC VIEW: For all visitors
     */
    public function index()
    {
        return Inertia::render('Gallery/index', [
            'images' => Image::latest()->get()
        ]);
    }

    /**
     * ADMIN VIEW: For gallery management
     */
    public function adminIndex()
    {
        return Inertia::render('Admin/Gallery', [
            'images' => Image::latest()->get()
        ]);
    }

    /**
     * ADMIN ACTION: Store new image
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

        return back(303);
    }

    /**
     * ADMIN ACTION: Delete image & file
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $filePath = str_replace('/storage/', '', $image->url);
        
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $image->delete();
        return back(303);
    }

    /**
     * INTERACTION: Increment Likes/Views
     */
    public function incrementView(Request $request, $id) 
    {
        $image = Image::find($id);
        if ($image) { $image->increment('views_count'); }
        return back(303);
    }

    public function incrementLike(Request $request, $id) 
    {
        $image = Image::find($id);
        if ($image) { $image->increment('likes_count'); }
        return back(303);
    }
}