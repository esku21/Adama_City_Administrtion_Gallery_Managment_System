<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Http\Request;

// Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController; 
use App\Http\Controllers\Auth\GuideLoginController; 
use App\Http\Controllers\ProfileController;

// Portal Controllers
use App\Http\Controllers\Visitor\BookingController as VisitorBooking; 
use App\Http\Controllers\Visitor\NotificationController;
use App\Http\Controllers\Visitor\FeedbackController as VisitorFeedback; 

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\SystemStatusController; 
use App\Http\Controllers\Admin\AdminGuideController; 
use App\Http\Controllers\Admin\FeedbackController as AdminFeedback;

// Guide Specific Controllers
use App\Http\Controllers\Guide\GuideController as StaffPortalController;
use App\Http\Controllers\Guide\GuideProfileController;
use App\Http\Controllers\Guide\GuideSettingsController;

/*
|--------------------------------------------------------------------------
| MAINTENANCE & BYPASS ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/force-admin-login', function () {
    $admin = User::where('role', 'admin')->first();
    if (!$admin) return redirect()->route('fix-admin');
    Auth::login($admin);
    return redirect()->route('admin.dashboard');
});

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
| GUEST ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::guard('web')->check()) return redirect()->route('dashboard');
    if (Auth::guard('guide')->check()) return redirect()->route('guide.dashboard');
    return Inertia::render('Visitor/GuestDashboard');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Guide Guest Routes
Route::middleware('guest:guide')->group(function () {
    Route::get('/guide/login', [GuideLoginController::class, 'showLoginForm'])->name('guide.login');
    Route::post('/guide/login', [GuideLoginController::class, 'login'])->name('guide.login.submit');
});

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    Auth::guard('guide')->logout();
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    /* Admin Group */
    Route::middleware(['can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'allBookings'])->name('dashboard'); 
        Route::get('/reports', [AdminDashboard::class, 'reports'])->name('reports'); 

        Route::get('/bookings', [AdminBooking::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [AdminBooking::class, 'store'])->name('bookings.store'); 
        Route::put('/bookings/{booking}', [AdminBooking::class, 'update'])->name('bookings.update');
        Route::patch('/bookings/{booking}/approve', [AdminBooking::class, 'approve'])->name('bookings.approve');
        Route::delete('/bookings/{booking}', [AdminBooking::class, 'destroy'])->name('bookings.delete');

        Route::get('/guides', [AdminGuideController::class, 'index'])->name('guides.index');
        Route::post('/guides', [AdminGuideController::class, 'store'])->name('guides.store');
        Route::put('/guides/{id}', [AdminGuideController::class, 'update'])->name('guides.update');
        Route::delete('/guides/{id}', [AdminGuideController::class, 'destroy'])->name('guides.delete');

        Route::get('/feedbacks', [AdminFeedback::class, 'index'])->name('feedbacks.index');
        Route::delete('/feedbacks/{id}', [AdminFeedback::class, 'destroy'])->name('feedbacks.delete');

        // FIX: Added explicit admin settings route
        Route::get('/settings', function () { 
            return Inertia::render('Admin/Settings'); 
        })->name('settings');

        Route::get('/system-status', [SystemStatusController::class, 'index'])->name('system.index');
        Route::post('/system-status/update', [SystemStatusController::class, 'update'])->name('system.update');
    });

    /* Visitor Group */
    Route::middleware(['can:access-visitor'])->prefix('visitor')->name('visitor.')->group(function () {
        Route::get('/dashboard', [VisitorBooking::class, 'index'])->name('dashboard');
        Route::get('/bookings/create', [VisitorBooking::class, 'create'])->name('booking.create');
        Route::post('/bookings/store', [VisitorBooking::class, 'store'])->name('booking.store');
        Route::get('/history', [VisitorBooking::class, 'history'])->name('history');
        Route::get('/booking/{booking}/download', [VisitorBooking::class, 'downloadTicket'])->name('booking.download');
        Route::delete('/booking/{booking}', [VisitorBooking::class, 'destroy'])->name('booking.destroy');
        Route::get('/feedback/create', [VisitorFeedback::class, 'create'])->name('feedback.create');
        Route::post('/feedback/store', [VisitorFeedback::class, 'store'])->name('feedback.store');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/settings', function () { return Inertia::render('Visitor/Settings'); })->name('settings.index');
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
    Route::get('/profile', [GuideProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [GuideSettingsController::class, 'edit'])->name('settings.index');
    Route::put('/settings/password', [GuideSettingsController::class, 'updatePassword'])->name('settings.password.update');
});