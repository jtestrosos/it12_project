<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Get the currently authenticated user from any guard.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function user()
    {
        if (Auth::guard('patient')->check()) {
            return Auth::guard('patient')->user();
        }
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        if (Auth::guard('super_admin')->check()) {
            return Auth::guard('super_admin')->user();
        }
        return null;
    }

    /**
     * Check if any user is logged in.
     *
     * @return bool
     */
    public static function check()
    {
        return self::user() !== null;
    }

    /**
     * Get the ID of the currently authenticated user across all guards.
     *
     * @return int|null
     */
    public static function id()
    {
        $user = self::user();
        return $user ? $user->id : null;
    }
}
