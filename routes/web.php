<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
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

// 🧠 Authentication Routes
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
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
    ]);

    Auth::login($user);
    return redirect('/');
});
