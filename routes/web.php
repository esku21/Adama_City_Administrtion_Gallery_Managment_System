<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

// Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\GuideLoginController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AboutController;

// Portal Controllers (Visitor)
use App\Http\Controllers\Visitor\BookingController as VisitorBooking; 
use App\Http\Controllers\Visitor\NotificationController;
use App\Http\Controllers\Visitor\FeedbackController as VisitorFeedback; 

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\SystemStatusController; 
use App\Http\Controllers\Admin\AdminGuideController; 
use App\Http\Controllers\Admin\FeedbackController as AdminFeedback;
use App\Http\Controllers\Admin\HallController; 
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\ReportController as AdminReportLogic;

// Shared/Unified Controllers
use App\Http\Controllers\AnnouncementController;

// Guide Specific Controllers
use App\Http\Controllers\Guide\GuideController as StaffPortalController;
use App\Http\Controllers\Guide\GuideProfileController;
use App\Http\Controllers\Guide\GuideSettingsController;
use App\Http\Controllers\Guide\FeedbackController as GuideFeedback;

/*
|--------------------------------------------------------------------------
| MAINTENANCE & BYPASS ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/force-admin-login', function (Request $request) {
    $admin = User::where('role', 'admin')->first();
    if (!$admin) return redirect()->route('fix-admin');
    Auth::guard('web')->login($admin);
    $request->session()->regenerate();
    return redirect()->route('admin.dashboard');
})->name('force-admin-login');

Route::get('/force-guide-login', function (Request $request) {
    $guide = \App\Models\Guide::where('is_active', true)->first();
    if (!$guide) {
        return redirect()->back()->with('error', 'No active guide account found.');
    }
    Auth::guard('guide')->login($guide);
    $request->session()->regenerate();
    return redirect()->route('guide.dashboard');
})->name('force-guide-login');

Route::get('/fix-admin', function () {
    User::updateOrCreate(
        ['email' => 'admin@acagms.com'],
        [
            'firstName' => 'Admin',
            'lastName' => 'User',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'visitorType' => 'Staff',
            'email_verified_at' => now(),
        ]
    );
    return "Admin account synced! Login with: admin@acagms.com / admin123";
})->name('fix-admin');

Route::get('/maintenance', function () {
    return Inertia::render('Errors/Maintenance');
})->name('maintenance.page');

/*
|--------------------------------------------------------------------------
| GUEST & PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::guard('web')->check()) return redirect()->route('dashboard');
    if (Auth::guard('guide')->check()) return redirect()->route('guide.dashboard');
    return Inertia::render('Visitor/GuestDashboard');
})->name('home');

// Public Pages
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contacts', fn() => Inertia::render('Public/Contacts'))->name('contacts');

// Public Gallery Actions (Visitor view)
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::post('/gallery/{id}/view', [GalleryController::class, 'incrementView'])->name('gallery.view');
Route::post('/gallery/{id}/like', [GalleryController::class, 'incrementLike'])->name('gallery.like');

// Public Reports
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

/*
|--------------------------------------------------------------------------
| VISITOR AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    Route::get('/forgot-password', fn() => Inertia::render('Auth/ForgotPassword'))->name('password.request');
    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::broker()->sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Reset link sent to your email!')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    Route::post('/verify-user-email', function (Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);
        return Redirect::back()->with('status', 'verified'); 
    })->name('email.verify.instant');

    Route::post('/reset-password-now', function (Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return Redirect::back()->with('status', 'success');
    })->name('password.update.instant');
});

/*
|--------------------------------------------------------------------------
| GUIDE AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::prefix('guide')->name('guide.')->group(function () {
    Route::middleware('guest:guide')->group(function () {
        Route::get('/login', [GuideLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [GuideLoginController::class, 'login'])->name('login.submit');
        Route::get('/forgot-password', [GuideLoginController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [GuideLoginController::class, 'sendResetLink'])->name('password.email');
    });
    Route::get('/verify-password/{guide}', [GuideLoginController::class, 'verifyAndGeneratePassword'])->name('password.verify.callback');
});

Route::post('/logout', function (Request $request) {
    if (Auth::guard('guide')->check()) {
        Auth::guard('guide')->logout();
    } else {
        Auth::guard('web')->logout();
    }
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (WEB GUARD)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', 'check.system'])->group(function () {

    Route::get('/dashboard', function () {
        $role = strtolower(Auth::user()->role ?? 'visitor');
        return ($role === 'admin') ? redirect()->route('admin.dashboard') : redirect()->route('visitor.dashboard');
    })->name('dashboard');

    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    
    /* --- ADMIN GROUP --- */
    Route::middleware(['can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'allBookings'])->name('dashboard'); 
        Route::get('/reports', [AdminReportLogic::class, 'index'])->name('reports'); 

        // Gallery Admin Management (Unified & Corrected)
        Route::patch('/gallery/{id}/stats', [GalleryController::class, 'updateStats'])->name('gallery.update-stats');
        Route::get('/gallery', [GalleryController::class, 'adminIndex'])->name('gallery.index');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::post('/gallery/{id}', [GalleryController::class, 'update'])->name('gallery.update'); // FIX: Added Update Route
        Route::delete('/gallery/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::post('/alerts/broadcast', [AlertController::class, 'broadcast'])->name('alerts.broadcast');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/halls', [HallController::class, 'index'])->name('halls.index');
        Route::post('/halls', [HallController::class, 'store'])->name('halls.store');
        Route::put('/halls/{hall}', [HallController::class, 'update'])->name('halls.update');
        Route::delete('/halls/{hall}', [HallController::class, 'destroy'])->name('halls.destroy');

        Route::get('/guides', [AdminGuideController::class, 'index'])->name('guides.index');
        Route::post('/guides', [AdminGuideController::class, 'store'])->name('guides.store');
        Route::put('/guides/{guide}', [AdminGuideController::class, 'update'])->name('guides.update');
        Route::delete('/guides/{guide}', [AdminGuideController::class, 'destroy'])->name('guides.destroy');
        Route::post('/guides/bulk', [AdminGuideController::class, 'import'])->name('guides.bulk-store');

        Route::get('/settings', fn() => Inertia::render('Admin/Settings'))->name('settings');
        Route::get('/system-status', [SystemStatusController::class, 'index'])->name('system.index');
        Route::post('/system-status/update', [SystemStatusController::class, 'update'])->name('system.update');
        
        Route::get('/bookings', [AdminBooking::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [AdminBooking::class, 'store'])->name('bookings.store'); 
        Route::put('/bookings/{booking}', [AdminBooking::class, 'update'])->name('bookings.update');
        Route::patch('/bookings/{booking}/approve', [AdminBooking::class, 'approve'])->name('bookings.approve');
        Route::delete('/bookings/{booking}', [AdminBooking::class, 'destroy'])->name('bookings.destroy');
        
        Route::get('/feedbacks', [AdminFeedback::class, 'index'])->name('feedbacks.index');
        Route::delete('/feedbacks/{id}', [AdminFeedback::class, 'destroy'])->name('feedbacks.destroy');
    });

    /* --- VISITOR GROUP --- */
    Route::middleware(['can:access-visitor'])->prefix('visitor')->name('visitor.')->group(function () {
        Route::get('/dashboard', [VisitorBooking::class, 'index'])->name('dashboard');
        
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        
        Route::get('/bookings/create', [VisitorBooking::class, 'create'])->name('booking.create');
        Route::post('/bookings/store', [VisitorBooking::class, 'store'])->name('booking.store');
        Route::get('/history', [VisitorBooking::class, 'history'])->name('history');
        Route::get('/booking/{id}/download', [VisitorBooking::class, 'downloadTicket'])->name('booking.download');
        Route::delete('/booking/{booking}', [VisitorBooking::class, 'destroy'])->name('booking.destroy');
        
        Route::get('/feedback/create', [VisitorFeedback::class, 'create'])->name('feedback.create');
        Route::post('/feedback/store', [VisitorFeedback::class, 'store'])->name('feedback.store');
        
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::delete('/notifications/destroy-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy'); 
        
        Route::get('/settings', fn() => Inertia::render('Visitor/Settings'))->name('settings.index');
    });
});

/*
|--------------------------------------------------------------------------
| GUIDE GUARD ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:guide', 'check.system'])->prefix('guide')->name('guide.')->group(function () {
    Route::get('/dashboard', [StaffPortalController::class, 'index'])->name('dashboard');
    Route::patch('/bookings/{id}', [StaffPortalController::class, 'updateStatus'])->name('bookings.update');
    Route::get('/scanner', [StaffPortalController::class, 'scanner'])->name('scanner');
    Route::post('/verify', [StaffPortalController::class, 'verifyTicket'])->name('verify'); 
    
    Route::get('/feedbacks', [GuideFeedback::class, 'index'])->name('feedbacks.index');
    Route::delete('/feedbacks/{id}', [GuideFeedback::class, 'destroy'])->name('feedbacks.destroy');

    Route::get('/profile', [GuideProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/image', [GuideProfileController::class, 'updateImage'])->name('profile.image');
    
    Route::get('/settings', [GuideSettingsController::class, 'edit'])->name('settings.index');
    Route::put('/settings/password', [GuideSettingsController::class, 'updatePassword'])->name('settings.password.update');
});