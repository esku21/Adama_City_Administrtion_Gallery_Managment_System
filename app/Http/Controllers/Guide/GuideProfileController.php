<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GuideProfileController extends Controller
{
    /**
     * Show the guide's profile information.
     */
    public function edit(): Response
    {
        $guide = Auth::guard('guide')->user();
        
        // Load the assigned hall name
        $guide->load('hall');

        return Inertia::render('Guide/Profile', [
            'guide' => $guide,
            'hall' => $guide->hall
        ]);
    }
}