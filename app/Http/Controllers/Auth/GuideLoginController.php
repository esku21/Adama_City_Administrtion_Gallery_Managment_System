<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Guide;
use App\Mail\GuidePasswordVerifyMail; 
use App\Mail\GuideAssignedMail;       
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class GuideLoginController extends Controller
{
    /**
     * Show the login form for guides.
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/GuideLogin', [
            'halls' => Hall::select('id', 'name')->get(),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle guide login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'hall_id' => 'required|exists:halls,id',
        ]);

        $guide = Guide::where('email', $request->email)->first();

        if (!$guide || !$guide->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account is inactive or does not exist.',
            ]);
        }

        // Validate that the guide belongs to the selected hall
        if ((int)$guide->hall_id !== (int)$request->hall_id) {
            throw ValidationException::withMessages([
                'hall_id' => 'Your account is not assigned to the selected gallery/hall.',
            ]);
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('guide')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('guide.dashboard'))->with('flash', [
                'message' => 'Welcome back, ' . $guide->name,
                'type' => 'success'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'The password provided is incorrect.',
        ]);
    }

    /**
     * Log the guide out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('guide')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guide.login');
    }

    /**
     * --- FORGOT PASSWORD FLOW ---
     */

    /**
     * Show the page to request a password reset.
     */
    public function showForgotPasswordForm(): Response
    {
        return Inertia::render('Auth/GuideForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Send the "Yes it is me" verification link.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:guides,email'
        ], [
            'email.exists' => 'No guide account found with this email address.'
        ]);

        $guide = Guide::where('email', $request->email)->first();

        // Create a temporary signed URL valid for 30 minutes
        $verificationUrl = URL::temporarySignedRoute(
            'guide.password.verify.callback', 
            now()->addMinutes(30), 
            ['guide' => $guide->id]
        );

        // Send the initial verification email (Markdown Template)
        Mail::to($guide->email)->send(new GuidePasswordVerifyMail($guide, $verificationUrl));

        return back()->with('status', 'Verification link sent! Please check your email to confirm it is you.');
    }

    /**
     * Verify the link click, generate 8-char password, and send final email.
     */
    public function verifyAndGeneratePassword(Request $request, Guide $guide): RedirectResponse
    {
        // 1. Security check: Validate the URL signature
        if (! $request->hasValidSignature()) {
            abort(401, 'This verification link has expired or is invalid.');
        }

        // 2. Generate a random 8-character password
        $newPassword = Str::random(8);

        // 3. Update the Guide's password in the database
        // Ensure 'password' is in the $fillable array of the Guide Model
        $guide->update([
            'password' => Hash::make($newPassword)
        ]);

        // 4. Send the final email with the actual new password (HTML Template)
        // This is sent immediately because we removed ShouldQueue from the Mail class
        Mail::to($guide->email)->send(new GuideAssignedMail($guide, $newPassword));

        return redirect()->route('guide.login')->with('status', 'Identity verified! Your new secure password has been sent to your email.');
    }
}