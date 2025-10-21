<?php
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');
// Booking Policy Page
Route::get('/policy', function () {
    return view('policy');
})->name('policy');

// Contact Us Page
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Book Appointment Page
Route::get('/booking', function () {
    return view('partials.book-appointment');
})->name('booking');

// Services Page
Route::get('/services', function () {
    return view('partials.services');
})->name('services');

// How It Works Page
Route::get('/how-it-works', function () {
    return view('partials.how-it-works');
})->name('how-it-works');
