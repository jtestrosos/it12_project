<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/test-superadmin', function () {
    $user = User::where('email', 'superadmin@malasakit.com')->first();
    if ($user) {
        return response()->json([
            'user' => $user->toArray(),
            'isSuperAdmin' => $user->isSuperAdmin(),
            'role' => $user->role
        ]);
    }
    return response()->json(['error' => 'User not found']);
});
