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

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
