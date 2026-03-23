<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings', [
            // Example settings data - you can fetch this from a 'Settings' model if you have one
            'settings' => [
                'system_status' => 'active',
                'feedback_status' => 'active',
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'system_status' => ['required', 'string'],
        ]);

        // Update Security Info
        $user->email = $request->email;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Note: System status (online/maintenance) would usually be saved to a different table
        // or a settings file. For now, we are syncing the user security.

        return back()->with('status', 'settings-updated');
    }
}