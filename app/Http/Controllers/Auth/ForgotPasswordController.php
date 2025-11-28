<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    private function findUserByEmail($email)
    {
        $patient = Patient::where('email', $email)->first();
        if ($patient)
            return $patient;

        $admin = Admin::where('email', $email)->first();
        if ($admin)
            return $admin;

        $superAdmin = SuperAdmin::where('email', $email)->first();
        if ($superAdmin)
            return $superAdmin;

        return null;
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = $this->findUserByEmail($request->email);

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        $otp = rand(100000, 999999);
        $email = $request->email;

        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        Mail::to($email)->send(new OtpMail($otp));

        return redirect()->route('password.otp', ['email' => $email])->with('status', 'OTP sent to your email.');
    }

    public function showOtpForm(Request $request)
    {
        return view('auth.passwords.otp', ['email' => $request->email]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = $this->findUserByEmail($request->email);
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        $record = DB::table('otps')->where('email', $request->email)->first();

        if (!$record || $record->otp != $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'OTP has expired.']);
        }

        return redirect()->route('password.reset', ['email' => $request->email, 'otp' => $request->otp]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.passwords.reset', ['email' => $request->email, 'otp' => $request->otp]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{}|,.<>\/?]).+$/',
            ],
        ], [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, and one special character.',
        ]);

        // Re-verify OTP to prevent direct access
        $record = DB::table('otps')->where('email', $request->email)->first();

        if (!$record || $record->otp != $request->otp || Carbon::parse($record->expires_at)->isPast()) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid or expired OTP session. Please try again.']);
        }

        $user = $this->findUserByEmail($request->email);

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('otps')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully. You can now login.');
    }
}
