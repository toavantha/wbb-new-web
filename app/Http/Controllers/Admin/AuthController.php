<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureCmsSessionIsActive;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials + ['is_approved' => true])) {
            if (Auth::user()->hasVerifiedEmail()) {
                $request->session()->regenerate();
                $request->session()->put(EnsureCmsSessionIsActive::LAST_ACTIVITY_SESSION_KEY, now()->timestamp);
                $request->user()->forceFill(['last_login_at' => now()])->save();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, '.Auth::user()->name);
            }

            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our administrative records, or your account is not verified and approved.',
        ])->onlyInput('email');
    }

    public function registerForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $validated['role'] = 'editor';
        $validated['is_approved'] = false;
        $user = User::create($validated);
        $user->sendEmailVerificationNotification();

        return redirect()->route('admin.login')->with(
            'success',
            'Check your email to verify your address. Once verified, an administrator must approve your account before you can sign in.'
        );
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been successfully logged out.');
    }
}
