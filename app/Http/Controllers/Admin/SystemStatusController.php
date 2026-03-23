<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SystemStatusController extends Controller
{
    public function index()
    {
        $settings = Setting::firstOrCreate(
            ['id' => 1],
            ['system_status' => 'active']
        );

        // Matches: resources/js/Pages/Admin/Settings/Index.vue
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'auth' => [
                'user' => Auth::user(),
            ],
            // This ensures the Toast shows up using the 'message' key
            'flash' => [
                'message' => session('message') ?? session('status'),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_status' => ['required', 'string', 'in:active,inactive,maintenance'],
        ]);

        Setting::updateOrCreate(
            ['id' => 1],
            ['system_status' => $request->system_status]
        );

        return Redirect::back()->with('message', 'System protocol successfully updated.');
    }
}