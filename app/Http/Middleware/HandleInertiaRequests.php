<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],

            'settings' => function () {
                $dbSettings = Setting::first();
                return [
                    'system_status' => $dbSettings->system_status ?? 'active',
                    'feedback_status' => $dbSettings->feedback_status ?? 'active',
                ];
            },
            
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'message' => fn () => $request->session()->get('message') ?? $request->session()->get('status'),
            ],

            'ziggy' => function () use ($request) {
                // Check if class exists before calling it to prevent the 500 error
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