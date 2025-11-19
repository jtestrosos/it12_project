<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Booking Policy Page
Route::get('/policy', fn() => view('policy'))->name('policy');

// Contact Us Page
Route::get('/contact', fn() => view('contact'))->name('contact');

// Services Page (static)
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
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = \App\Models\User::where('email', $request->input('email'))->first();

    if (!$user) {
        $errors = ['email' => 'Invalid Credentials.'];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['errors' => $errors], 422);
        }

        return back()->withErrors($errors)->withInput($request->only('email'));
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();

        // Redirect based on user role
        $redirectUrl = route('patient.dashboard');
        if ($user->isSuperAdmin()) {
            $redirectUrl = route('superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            $redirectUrl = route('admin.dashboard');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect()->to($redirectUrl);
    }

    $errors = ['password' => 'Incorrect password. Please try again.'];

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(['errors' => $errors], 422);
    }

    return back()->withErrors($errors)->withInput($request->only('email'));
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s\.\-\']+$/',
        ],
        'email' => 'required|email|unique:users,email',
        'gender' => 'required|in:male,female,other',
        'phone' => [
            'nullable',
            'digits:11',
        ],
        'address' => 'nullable|string|max:500',
        'barangay' => [
            'required',
            Rule::in(['Barangay 11', 'Barangay 12', 'Other']),
        ],
        'barangay_other' => [
            'nullable',
            'string',
            'max:255',
            Rule::requiredIf(fn () => $request->barangay === 'Other'),
        ],
        'purok' => [
            'nullable',
            Rule::requiredIf(fn () => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
            Rule::when(
                $request->barangay === 'Barangay 11',
                Rule::in(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'])
            ),
            Rule::when(
                $request->barangay === 'Barangay 12',
                Rule::in(['Purok 1', 'Purok 2', 'Purok 3'])
            ),
        ],
        'birth_date' => [
            'required',
            'date',
            'before:today',
        ],
        'password' => [
            'required',
            'min:8',
            'confirmed',
            // At least one lowercase, one uppercase, and one special character (subset without quotes to keep regex simple)
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).+$/',
        ],
    ], [
        'name.regex' => 'The name field should not contain numbers. Only letters, spaces, periods, hyphens, and apostrophes are allowed.',
        'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
        'phone.digits' => 'Phone number must be exactly 11 digits (e.g. 09123456789).',
        'gender.required' => 'Please select a gender.',
        'barangay.in' => 'Please select Barangay 11, Barangay 12, or choose Other.',
        'barangay_other.required' => 'Please specify your barangay.',
        'purok.required' => 'Please select a purok for the chosen barangay.',
        'purok.in' => 'Please choose a valid purok option.',
        'birth_date.before' => 'Birth date must be in the past.',
        'password.confirmed' => 'Password and confirm password must match.',
    ]);

    $age = Carbon::parse($validated['birth_date'])->age;

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'gender' => $validated['gender'],
        'phone' => $validated['phone'] ?? null,
        'address' => $validated['address'] ?? null,
        'barangay' => $validated['barangay'],
        'barangay_other' => $validated['barangay'] === 'Other' ? $validated['barangay_other'] : null,
        'purok' => $validated['barangay'] === 'Other' ? null : ($validated['purok'] ?? null),
        'birth_date' => $validated['birth_date'],
        'age' => $age,
        'password' => bcrypt($validated['password']),
        'role' => 'user' // Default role for new registrations
    ]);

    Auth::login($user);
    return redirect()->route('patient.dashboard');
});

// Patient Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');
        Route::get('/book-appointment', [PatientController::class, 'bookAppointment'])->name('book-appointment');
        Route::post('/book-appointment', [PatientController::class, 'storeAppointment'])->name('store-appointment');
        Route::get('/appointments/slots', [PatientController::class, 'getAvailableSlots'])->name('appointments.slots');
        Route::get('/appointments/calendar', [PatientController::class, 'getCalendarData'])->name('appointments.calendar');
        Route::get('/appointment/{appointment}', [PatientController::class, 'showAppointment'])->name('appointment.show');
        Route::post('/appointment/{appointment}/cancel', [PatientController::class, 'cancelAppointment'])->name('appointment.cancel');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
            Route::post('/patient/create', [AdminController::class, 'createPatient'])->name('patient.create');
            Route::put('/patient/{user}/update', [AdminController::class, 'updatePatient'])->name('patient.update');
            Route::post('/patient/{user}/archive', [AdminController::class, 'archivePatient'])->name('patient.archive');
            Route::get('/patients/archive', [AdminController::class, 'archivedPatients'])->name('patients.archive');
            Route::post('/patient/{id}/restore', [AdminController::class, 'restorePatient'])->name('patient.restore');
            Route::delete('/patient/{id}/force-delete', [AdminController::class, 'forceDeletePatient'])->name('patient.force-delete');
            Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
            Route::post('/appointment/create', [AdminController::class, 'createAppointment'])->name('appointment.create');
            Route::post('/appointment/{appointment}/update', [AdminController::class, 'updateAppointmentStatus'])->name('appointment.update');
            Route::get('/appointments/slots', [AdminController::class, 'getAvailableSlots'])->name('appointments.slots');
            Route::get('/appointments/calendar', [AdminController::class, 'getCalendarData'])->name('appointments.calendar');
            Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
            Route::post('/inventory/add', [AdminController::class, 'addInventory'])->name('inventory.add');
            Route::post('/inventory/{inventory}/update', [AdminController::class, 'updateInventory'])->name('inventory.update');
            Route::post('/inventory/{inventory}/restock', [AdminController::class, 'restockInventory'])->name('inventory.restock');
            Route::post('/inventory/{inventory}/deduct', [AdminController::class, 'deductInventory'])->name('inventory.deduct');
            Route::post('/walk-in', [AdminController::class, 'addWalkIn'])->name('walk-in');
            Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
            Route::get('/reports/export/appointments', [AdminController::class, 'exportAppointmentsExcel'])->name('reports.export.appointments');
            Route::get('/reports/export/appointments/pdf', [AdminController::class, 'exportAppointmentsPdf'])->name('reports.export.appointments.pdf');
        });
});

// Super Admin Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::get('/users/archive', [SuperAdminController::class, 'archivedUsers'])->name('users.archive');
        Route::post('/user/create', [SuperAdminController::class, 'createUser'])->name('user.create');
        Route::post('/user/{user}/update', [SuperAdminController::class, 'updateUser'])->name('user.update');
        Route::post('/user/{user}/delete', [SuperAdminController::class, 'deleteUser'])->name('user.delete');
        Route::post('/user/{id}/restore', [SuperAdminController::class, 'restoreUser'])->name('user.restore');
        Route::delete('/user/{id}/force-delete', [SuperAdminController::class, 'forceDeleteUser'])->name('user.force-delete');
        Route::get('/system-logs', [SuperAdminController::class, 'systemLogs'])->name('system-logs');
        Route::get('/audit-trail', [SuperAdminController::class, 'auditTrail'])->name('audit-trail');
        Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
        Route::get('/backup', [SuperAdminController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SuperAdminController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/schedule', [SuperAdminController::class, 'scheduleBackup'])->name('backup.schedule');
        Route::get('/backup/{backup}/download', [SuperAdminController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{backup}/delete', [SuperAdminController::class, 'deleteBackup'])->name('backup.delete');
    });
});
