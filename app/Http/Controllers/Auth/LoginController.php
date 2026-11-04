<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Handles user authentication (login/logout).
 */
class LoginController
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $loginInput = $credentials['email'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Determine if input is formatted as an email
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        $primaryCredentials = [
            $isEmail ? 'email' : 'username' => $loginInput,
            'password' => $password,
        ];

        $authenticated = Auth::attempt($primaryCredentials, $remember);

        // Fallback: if not authenticated and not email format, or vice-versa
        if (! $authenticated) {
            $fallbackCredentials = [
                $isEmail ? 'username' : 'email' => $loginInput,
                'password' => $password,
            ];
            $authenticated = Auth::attempt($fallbackCredentials, $remember);
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        $user = Auth::user();

        // Admin redirect vs Standard User redirect
        if ($user && $user->role === UserRole::Admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Pending Google OAuth redirect placeholder.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return redirect()->route('login')->with(
            'error',
            'Google Sign-In is currently pending OAuth configuration. Please sign in with your email or username.'
        );
    }

    /**
     * Pending Google OAuth callback placeholder.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        return redirect()->route('login')->with(
            'error',
            'Google OAuth credentials are not configured. Please use standard email/username login.'
        );
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $throttleKey = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'email' => [
                trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
