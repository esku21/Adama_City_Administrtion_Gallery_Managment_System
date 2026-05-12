<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Visitor/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. IMPROVED VALIDATION
        // We use named keys in the custom messages to be very specific
        $request->validate([
            'firstName'   => [
                'required', 
                'string', 
                'min:3', 
                'max:20', 
                'regex:/^[a-zA-Z\s\-]+$/', // Rule 0: Letters only
                'regex:/[aeiouAEIOU]/'     // Rule 1: Must have a vowel
            ], 
            'lastName'    => [
                'required', 
                'string', 
                'min:3', 
                'max:20', 
                'regex:/^[a-zA-Z\s\-]+$/', 
                'regex:/[aeiouAEIOU]/'
            ],
            'email'       => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone_no'    => ['required', 'unique:users,phone_no', 'regex:/^(09|07)\d{8}$/'],
            'visitorType' => 'required|string|in:Local,Foreign',
            'citizenship' => 'nullable|string|max:100',
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // SPECIFIC MESSAGES FOR EACH FIELD
            'firstName.regex' => 'The First Name must be a valid name containing letters and at least one vowel.',
            'lastName.regex'  => 'The Last Name must be a valid name containing letters and at least one vowel.',
            'phone_no.regex'  => 'Enter a valid Ethiopian phone number starting with 09 or 07.',
            'email.unique'    => 'This email is already registered.',
        ]);

        DB::beginTransaction();

        try {
            // 2. DATA CLEANING
            $cleanFirst = ucfirst(strtolower(trim(strip_tags($request->firstName))));
            $cleanLast  = ucfirst(strtolower(trim(strip_tags($request->lastName))));

            // 3. CREATE THE USER
            $user = User::create([
                'firstName'   => $cleanFirst,
                'lastName'    => $cleanLast,
                'email'       => $request->email,
                'phone_no'    => $request->phone_no,
                'visitorType' => $request->visitorType,
                'password'    => Hash::make($request->password), 
                'role'        => 'visitor', 
                'citizenship' => $request->citizenship ?? 'Ethiopian',
            ]);

            // 4. CREATE THE VISITOR RECORD
            Visitor::create([
                'user_id'     => $user->id,
                'firstName'   => $user->firstName,
                'lastName'    => $user->lastName,
                'email'       => $user->email,
                'phone_no'    => $user->phone_no,
                'visitorType' => $user->visitorType,
            ]);

            DB::commit();
            event(new Registered($user));

            return redirect()->route('login')->with('success', 'Registration successful! Please login.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // This ensures if the database fails, you get a clean error back to the form
            return redirect()->back()
                ->withErrors(['firstName' => 'A system error occurred. Please try again.'])
                ->withInput();
        }
    }
}