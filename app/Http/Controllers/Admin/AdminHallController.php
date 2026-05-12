<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Hall;
use Illuminate\Support\Facades\Redirect;

class HallController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Halls/Index', [
            'halls' => Hall::latest()->get(),
            // Pass flash messages explicitly if not handled by Middleware
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:halls,name',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
        ]);

        Hall::create($validated);

        return Redirect::route('admin.halls.index')->with('success', 'New Hall added successfully!');
    }

    public function update(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:halls,name,' . $hall->id,
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
        ]);

        $hall->update($validated);

        return Redirect::route('admin.halls.index')->with('success', 'Hall updated successfully!');
    }

    public function destroy(Hall $hall)
    {
        // Safety check for relationships
        if (method_exists($hall, 'bookings') && $hall->bookings()->exists()) {
            return Redirect::back()->with('error', 'Cannot delete hall with existing bookings.');
        }

        $hall->delete();

        return Redirect::route('admin.halls.index')->with('success', 'Hall deleted successfully.');
    }
}