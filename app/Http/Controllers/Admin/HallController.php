<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Hall;
use Illuminate\Support\Facades\Redirect;

class HallController extends Controller
{
    /**
     * Display a listing of the halls.
     */
    public function index()
    {
        return Inertia::render('Admin/Halls/Index', [
            'halls' => Hall::latest()->get()
        ]);
    }

    /**
     * Store a newly created hall in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:halls,name',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'is_active'   => 'boolean', // Changed to optional to allow default value
        ]);

        // Default to active if not provided by the registry form
        $validated['is_active'] = $request->has('is_active') ? $request->is_active : true;

        Hall::create($validated);

        return Redirect::route('admin.halls.index')->with('success', 'Hall created successfully.');
    }

    /**
     * Update the specified hall in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:halls,name,' . $hall->id,
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $hall->update($validated);

        return Redirect::route('admin.halls.index')->with('success', 'Hall updated successfully.');
    }

    /**
     * Remove the specified hall from storage.
     */
    public function destroy(Hall $hall)
    {
        // Check for existing bookings to maintain system integrity
        // Note: Ensure your Hall Model has a bookings() relationship defined
        if (method_exists($hall, 'bookings') && $hall->bookings()->exists()) {
            return Redirect::back()->with('error', 'Cannot delete hall with existing bookings. Consider deactivating it instead.');
        }

        $hall->delete();

        return Redirect::route('admin.halls.index')->with('success', 'Hall deleted successfully.');
    }
}