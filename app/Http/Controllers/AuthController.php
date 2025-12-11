<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Handle user login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $email = $request->input('email');

        // Try to find user in all three tables
        $patient = Patient::where('email', $email)->first();
        $admin = Admin::where('email', $email)->first();
        $superAdmin = SuperAdmin::where('email', $email)->first();

        // Determine which guard to use
        $guard = null;
        $redirectUrl = null;
        $welcomeMessage = null;

        if ($patient) {
            $guard = 'patient';
            $redirectUrl = route('patient.dashboard');
        } elseif ($admin) {
            $guard = 'admin';
            $redirectUrl = route('admin.dashboard');
            $welcomeMessage = 'Welcome back, ' . $admin->name . '! You have successfully logged in.';
        } elseif ($superAdmin) {
            $guard = 'super_admin';
            $redirectUrl = route('superadmin.dashboard');
            $welcomeMessage = 'Welcome back, ' . $superAdmin->name . '! You have successfully logged in.';
        }

        // If no user found in any table
        if (!$guard) {
            $errors = ['password' => 'Invalid Credentials.'];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $errors], 422);
            }

            return back()->withErrors($errors)->withInput($request->only('email'));
        }

        // Attempt authentication with the appropriate guard
        if (Auth::guard($guard)->attempt($credentials)) {
            $request->session()->regenerate();

            if ($welcomeMessage) {
                session()->flash('announcement', $welcomeMessage);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['redirect' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl);
        }

        // Authentication failed
        $errors = ['password' => 'Incorrect password. Please try again.'];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['errors' => $errors], 422);
        }

        return back()->withErrors($errors)->withInput($request->only('email'));
    }

    /**
     * Check if email is available for registration.
     */
    public function checkEmailAvailability(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = Patient::where('email', $request->email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.'
        ]);
    }

    /**
     * Handle user registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => 'required|email|unique:patient,email',
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
                Rule::requiredIf(fn() => $request->barangay === 'Other'),
            ],
            'purok' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($request->barangay, ['Barangay 11', 'Barangay 12'], true)),
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
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
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
        ]);

        $age = Carbon::parse($validated['birth_date'])->age;

        $patient = Patient::create([
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
        ]);

        Auth::guard('patient')->login($patient);
        return redirect()->route('patient.dashboard');
    }

    /**
     * Handle user logout request.
     */
    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::guard('patient')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('super_admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
