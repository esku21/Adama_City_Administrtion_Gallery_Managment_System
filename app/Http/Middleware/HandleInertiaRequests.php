<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache; // Added for performance
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root view that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // 1. User Authentication Data & Badge Count
            'auth' => [
                'user' => $request->user(),
                
                // Optimized count query using closure to only run when needed
                'notifications_count' => fn () => $request->user() 
                    ? $request->user()->announcements()
                        ->wherePivot('is_read', false)
                        ->count() 
                    : 0,
            ],

            /**
             * 2. Announcements Inbox
             * We use a closure here so it doesn't slow down API-only requests.
             */
            'announcements' => fn () => $request->user() 
                ? $request->user()->announcements()
                    ->where('is_active', true)
                    ->latest('announcement_user.created_at')
                    ->limit(10) // Limit to 10 latest to keep payload small
                    ->get()
                : [],

            // 3. Global Site Settings (with Cache Support)
            'settings' => function () {
                return Cache::remember('site_settings', 3600, function () {
                    $settings = [
                        'system_status'   => 'active', 
                        'feedback_status' => 'active',
                        'emergency_mode'  => false
                    ];

                    if (Schema::hasTable('site_settings')) {
                        try {
                            $dbSettings = DB::table('site_settings')->get();
                            foreach ($dbSettings as $setting) {
                                $settings[$setting->key] = $setting->value;
                            }
                        } catch (\Exception $e) {
                            // Silently fail if table exists but query fails
                        }
                    }
                    return $settings;
                });
            },
            
            // 4. Flash Messages (Crucial for your Feedback Delete Popup)
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message') ?? $request->session()->get('status'),
            ],

            // 5. Ziggy Route Support
            'ziggy' => function () use ($request) {
                if (class_exists(Ziggy::class)) {
                    return array_merge((new Ziggy)->toArray(), [
                        'location' => $request->url(),
                    ]);
                }
                return ['location' => $request->url()];
            },
        ]);
    }
}