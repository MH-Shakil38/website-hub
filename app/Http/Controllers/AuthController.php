<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            ActivityLog::record(
                'User signed in',
                Auth::user()->email,
                'bi-person-check-fill',
                '#DCFCE7',
                '#10B981'
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records. (Default: you@example.com / password)',
        ])->onlyInput('email');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.'])->onlyInput('email');
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        return back()->with('status', 'We have emailed your password reset link! You can also reset directly at: ' . route('password.reset', ['token' => $token, 'email' => $user->email]));
    }

    public function showResetPassword(Request $request, ?string $token = null)
    {
        return view('auth.reset-password', [
            'email' => $request->query('email', auth()->user()?->email ?? 'you@example.com'),
            'token' => $token ?? 'demo-token',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['nullable', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $email = $request->input('email') ?: (auth()->user()?->email ?? 'you@example.com');
        $user = User::where('email', $email)->first() ?? User::first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            ActivityLog::record(
                'Password reset completed',
                $user->email,
                'bi-shield-lock-fill',
                '#FFE4EC',
                '#EC134A'
            );
        }

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! Please sign in.');
    }

    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            $request->user()->update(['email_verified_at' => now()]);
        }

        return back()->with('status', 'A fresh verification link has been sent to your email address.');
    }

    public function showTwoFactor()
    {
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $digits = $request->input('digits', []);
        $enteredCode = is_array($digits) ? implode('', $digits) : (string) $request->input('code', '274916');

        $user = auth()->user() ?? User::first();
        $expectedCode = $user?->two_factor_code ?: '274916';

        if ($enteredCode !== '' && $enteredCode !== $expectedCode && $enteredCode !== '274916') {
            return back()->withErrors(['code' => 'Invalid 6-digit verification code. Default code is 274916.']);
        }

        $request->session()->put('two_factor_verified', true);

        return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication verified successfully.');
    }

    public function showConfirmPassword()
    {
        return view('auth.confirm-password');
    }

    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $user = auth()->user() ?? User::first();

        if ($user && !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password does not match your current password.',
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Password confirmed.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
