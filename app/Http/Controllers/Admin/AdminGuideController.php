<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide; 
use App\Models\Hall;
use App\Mail\GuideAssignedMail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminGuideController extends Controller
{
    /**
     * Display the list of guides and halls.
     */
    public function index(): Response
    {
        $guides = Guide::with('hall')->latest()->get();
        $halls = Hall::select('id', 'name')->get(); 

        return Inertia::render('Admin/Guides/Index', [
            'guides' => $guides,
            'halls' => $halls
        ]);
    }

    /**
     * Store a single new guide.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:guides,email',
            'phone'   => 'required|string|max:20',
            'gender'  => 'required|string|in:Male,Female',
            'hall_id' => 'nullable|exists:halls,id', 
        ]);

        $plainPassword = Str::random(8);

        $guide = Guide::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'gender'    => $validated['gender'],
            'hall_id'   => $validated['hall_id'],
            'password'  => Hash::make($plainPassword), 
            'is_active' => true,
        ]);

        $this->sendWelcomeEmail($guide, $plainPassword);

        return redirect()->back()->with('flash', [
            'message' => "Guide created! Password: {$plainPassword}",
            'type'    => 'success'
        ]);
    }

    /**
     
     * CSV Format: name, email, phone, gender, hall_name
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row if exists
        fgetcsv($handle); 

        $count = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Mapping: 0:name, 1:email, 2:phone, 3:gender, 4:hall_name
                $hallName = trim($data[4] ?? '');
                $hall = Hall::where('name', 'LIKE', $hallName)->first();

                if (!$hall && !empty($hallName)) {
                    $errors[] = "Row for {$data[0]}: Hall '{$hallName}' not found.";
                    continue;
                }

                $plainPassword = Str::random(8);
                
                $guide = Guide::create([
                    'name'     => $data[0],
                    'email'    => $data[1],
                    'phone'    => $data[2],
                    'gender'   => $data[3],
                    'hall_id'  => $hall?->id,
                    'password' => Hash::make($plainPassword),
                    'is_active' => true,
                ]);

                $this->sendWelcomeEmail($guide, $plainPassword);
                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import Error: " . $e->getMessage());
            return redirect()->back()->with('flash', ['message' => 'Critical error during import.', 'type' => 'error']);
        }

        fclose($handle);

        $msg = "Imported {$count} guides successfully.";
        if (count($errors) > 0) $msg .= " Errors: " . implode(' ', $errors);

        return redirect()->back()->with('flash', [
            'message' => $msg,
            'type'    => count($errors) > 0 ? 'warning' : 'success'
        ]);
    }

    /**
     * Helper to handle email logic.
     */
    private function sendWelcomeEmail($guide, $password)
    {
        try {
            Mail::to($guide->email)->queue(new GuideAssignedMail($guide, $password));
        } catch (\Exception $e) {
            Log::error("Mail failed: " . $e->getMessage());
            Mail::to($guide->email)->send(new GuideAssignedMail($guide, $password));
        }
    }

    /**
     * Resend a new password to an existing guide.
     */
    public function resendPassword(Guide $guide): RedirectResponse
    {
        $newPassword = Str::random(8);
        $guide->update(['password' => Hash::make($newPassword)]);

        $this->sendWelcomeEmail($guide, $newPassword);

        return redirect()->back()->with('flash', [
            'message' => "New password generated and emailed: {$newPassword}",
            'type'    => 'info'
        ]);
    }

    /**
     * Update existing guide details.
     */
    public function update(Request $request, Guide $guide): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:guides,email,' . $guide->id,
            'phone'   => 'required|string|max:20',
            'gender'  => 'required|string|in:Male,Female',
            'hall_id' => 'nullable|exists:halls,id',
            'is_active' => 'boolean'
        ]);

        $guide->update($validated);

        return redirect()->back()->with('flash', [
            'message' => 'Guide updated successfully.',
            'type'    => 'success'
        ]);
    }

    /**
     * Delete a guide.
     */
    public function destroy(Guide $guide): RedirectResponse
    {
        try {
            $guide->delete();
            return redirect()->back()->with('flash', [
                'message' => 'Guide removed from system.',
                'type'    => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('flash', [
                'message' => 'Delete failed: Guide might have active records.',
                'type'    => 'error'
            ]);
        }
    }
}