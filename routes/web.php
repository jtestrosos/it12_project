<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Booking Policy Page
Route::get('/policy', fn() => view('policy'))->name('policy');

// Contact Us Page
Route::get('/contact', fn() => view('contact'))->name('contact');

// Services Page
Route::get('/services', fn() => view('partials.services'))->name('services');

// How It Works Page
Route::get('/how-it-works', fn() => view('partials.how-it-works'))->name('how-it-works');

// Booking Page
Route::get('/booking', function () {
    if (!Auth::check()) {
        // User not logged in — show the home page with login modal open
        return redirect('/')->with('showLoginModal', true);
    }
    return view('partials.book-appointment');
})->name('booking');

// Authentication Routes
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        
        // Redirect based on user role
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    }
    return back()->withErrors(['email' => 'Invalid credentials, please try again.']);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'user' // Default role for new registrations
    ]);

    Auth::login($user);
    return redirect()->route('patient.dashboard');
});

// Patient Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/book-appointment', [PatientController::class, 'bookAppointment'])->name('book-appointment');
        Route::post('/appointment', [PatientController::class, 'storeAppointment'])->name('appointment.store');
        Route::get('/appointment/{appointment}', [PatientController::class, 'showAppointment'])->name('appointment.show');
        Route::post('/appointment/{appointment}/cancel', [PatientController::class, 'cancelAppointment'])->name('appointment.cancel');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::post('/appointment/{appointment}/update', [AdminController::class, 'updateAppointmentStatus'])->name('appointment.update');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
        Route::post('/inventory/add', [AdminController::class, 'addInventory'])->name('inventory.add');
        Route::post('/inventory/{inventory}/update', [AdminController::class, 'updateInventory'])->name('inventory.update');
        Route::post('/walk-in', [AdminController::class, 'addWalkIn'])->name('walk-in');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});

// Super Admin Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::post('/user/create', [SuperAdminController::class, 'createUser'])->name('user.create');
        Route::post('/user/{user}/update', [SuperAdminController::class, 'updateUser'])->name('user.update');
        Route::post('/user/{user}/delete', [SuperAdminController::class, 'deleteUser'])->name('user.delete');
        Route::get('/system-logs', [SuperAdminController::class, 'systemLogs'])->name('system-logs');
        Route::get('/audit-trail', [SuperAdminController::class, 'auditTrail'])->name('audit-trail');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
        Route::post('/backup', [SuperAdminController::class, 'backup'])->name('backup');
    });
});
