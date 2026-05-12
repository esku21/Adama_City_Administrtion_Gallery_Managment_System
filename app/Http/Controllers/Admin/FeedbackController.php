<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(): Response
    {
        $feedbacks = Feedback::with(['user', 'hall'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($fb) {
                $path = $fb->image_path;
                $urls = [];

                // 1. Decode JSON string into an array
                if (is_string($path)) {
                    $decoded = json_decode($path, true);
                    $paths = is_array($decoded) ? $decoded : [$path];
                } else {
                    $paths = is_array($path) ? $path : ($path ? [$path] : []);
                }

                // 2. Clean and format each path into a URL
                foreach ($paths as $p) {
                    if ($p) {
                        $cleanPath = trim($p, '[]" ');
                        $urls[] = str_replace('\\', '/', $cleanPath);
                    }
                }

                // Attach the full array of URLs to the feedback object
                $fb->image_urls = $urls;
                
                return $fb;
            });

        $summary = [
            'total_satisfied'   => Feedback::where('sentiment_status', 'Satisfaction')->count(),
            'total_unsatisfied' => Feedback::where('sentiment_status', 'UnSatisfaction')->count(),
            'total_natural'     => Feedback::where('sentiment_status', 'Natural')->count(),
        ];

        return Inertia::render('Admin/Feedbacks/Index', [
            'feedbacks' => $feedbacks,
            'summary'   => (object)$summary,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $feedback = Feedback::findOrFail($id);
            $feedback->delete();
            return redirect()->back()->with('success', 'Feedback removed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete.');
        }
    }
}