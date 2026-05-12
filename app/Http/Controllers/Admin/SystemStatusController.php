<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SystemStatusController extends Controller
{
    /**
     * Display the dedicated system status page.
     */
    public function index()
    {
        // Fetch status from the database
        $systemStatus = DB::table('site_settings')
            ->where('key', 'system_status')
            ->value('value') ?? 'active';

        return Inertia::render('Admin/SystemStatus', [
            'settings' => [
                'system_status' => $systemStatus,
            ],
            'auth' => [
                'user' => Auth::user(),
            ],
            'flash' => [
                'message' => session('message') ?? session('status') ?? session('success'),
            ]
        ]);
    }

    /**
     * Update the system status protocol.
     */
    public function update(Request $request)
    {
        $request->validate([
            'system_status' => ['required', 'string', 'in:active,inactive,maintenance'],
        ]);

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'system_status'],
            [
                'value' => $request->system_status,
                'updated_at' => now(),
            ]
        );

        return Redirect::back()->with('message', 'System protocol successfully updated to ' . strtoupper($request->system_status));
    }
}