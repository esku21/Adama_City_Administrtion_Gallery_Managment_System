<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AnnouncementController extends Controller 
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        return Inertia::render('Admin/Announcements', [
            'announcements' => Announcement::orderBy('created_at', 'desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new announcement.
     * Required to prevent "Call to undefined method" errors in web.php.
     */
    public function create()
    {
        return Inertia::render('Admin/Announcements');
    }

    /**
     * Store and Broadcast an Alert only to visitors booked for the specific date.
     */
    public function store(Request $request) 
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string', 
            'target_date'     => 'required|date',
            'reschedule_date' => 'nullable|date|after_or_equal:target_date',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create the Announcement record
            $announcement = Announcement::create([
                'title'           => $request->title,
                'content'         => $request->message, // Maps Vue 'message' to DB 'content'
                'target_date'     => $request->target_date,
                'reschedule_date' => $request->reschedule_date,
                'type'            => 'warning',
                'is_active'       => true,
            ]);

            // 2. SMART FILTER: Find only visitors who have a booking on that exact date
            // This prevents notifying users who are not affected by the change.
            $visitorIds = User::where('role', 'visitor')
                ->whereHas('bookings', function ($query) use ($request) {
                    $query->whereDate('booking_date', $request->target_date)
                          ->whereIn('status', ['pending', 'approved']);
                })
                ->pluck('id');
            
            // 3. Attach only those specific visitors via pivot table
            if ($visitorIds->isNotEmpty()) {
                $announcement->users()->syncWithoutDetaching($visitorIds);
            }

            DB::commit();

            $count = $visitorIds->count();
            return Redirect::route('admin.announcements.index')
                ->with('message', "Alert broadcasted successfully to $count affected visitors!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors([
                'error' => 'Broadcast failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified announcement and clean up relationships.
     */
    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            
            // Clean up the pivot table relationship first to avoid integrity issues
            $announcement->users()->detach(); 
            $announcement->delete();

            return Redirect::back()->with('message', 'Announcement removed.');
        } catch (\Exception $e) {
            return Redirect::back()->withErrors([
                'error' => 'Delete failed: ' . $e->getMessage()
            ]);
        }
    }
}