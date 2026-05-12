<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EmergencyAlertController extends Controller 
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements
        ]);
    }

    /**
     * Broadcast an Emergency Alert (store).
     */
    public function broadcast(Request $request) 
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string',
            'target_date'     => 'required|date|after_or_equal:today',
            'reschedule_date' => 'nullable|date|after:target_date',
        ]);

        try {
            DB::beginTransaction();

            // Note: DB column is 'content', Form field is 'message'
            $announcement = Announcement::create([
                'title'           => $request->title,
                'content'         => $request->message, 
                'target_date'     => $request->target_date,
                'reschedule_date' => $request->reschedule_date,
                'type'            => 'warning',
                'is_active'       => true,
            ]);

            // Target only visitors to notify them in their dashboard
            $visitorIds = User::where('role', 'visitor')->pluck('id');
            if ($visitorIds->isNotEmpty()) {
                $announcement->users()->syncWithoutDetaching($visitorIds);
            }

            DB::commit();
            return Redirect::route('admin.announcements.index')->with('message', 'Alert broadcasted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors(['error' => 'Broadcast failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an announcement.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->users()->detach(); // Clean up pivot table
        $announcement->delete();

        return Redirect::back()->with('message', 'Announcement deleted.');
    }
}