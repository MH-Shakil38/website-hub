<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

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
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Always answer the same way so the form can't be used to discover which emails exist.
        Password::sendResetLink($request->only('email'), function (User $user, string $token) {
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
            app(\App\Services\SaasMailService::class)->sendPasswordResetLink($user, $resetUrl);

            return Password::RESET_LINK_SENT;
        });

        return back()->with('status', 'If that email address is registered, we have emailed a password reset link to it.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'email' => (string) $request->query('email', ''),
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();

                ActivityLog::record(
                    'Password reset completed',
                    $user->email,
                    'bi-shield-lock-fill',
                    '#FFE4EC',
                    '#EC134A'
                );
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.'])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! Please sign in.');
    }

    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    public function resendVerification(Request $request)
    {
        // Only send the mail; never mark the account verified just because the user asked.
        app(\App\Services\SaasMailService::class)->sendEmailVerificationLink($request->user(), route('admin.dashboard'));

        return back()->with('status', 'A fresh verification link has been sent to your email address.');
    }

    public function showTwoFactor()
    {
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $digits = $request->input('digits', []);
        $entered = is_array($digits) ? implode('', $digits) : (string) $request->input('code', '');
        $expected = (string) ($request->user()->two_factor_code ?? '');

        // No code configured => nothing to verify against => never succeed.
        if ($expected === '' || !hash_equals($expected, $entered)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
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

        if (!Hash::check($request->password, $request->user()->password)) {
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
