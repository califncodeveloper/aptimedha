<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * Redirect the user to Google's OAuth 2.0 consent screen.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (empty($clientId) || empty($redirectUri)) {
            return redirect()->route('login')->with(
                'error',
                'Google OAuth credentials are not configured. Please sign in with your email or username.'
            );
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Handle the callback returned from Google OAuth 2.0.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with(
                'error',
                'Google Sign-In was cancelled or failed.'
            );
        }

        $savedState = $request->session()->pull('google_oauth_state');
        $callbackState = $request->input('state');

        if (empty($savedState) || empty($callbackState) || ! hash_equals($savedState, $callbackState)) {
            return redirect()->route('login')->with(
                'error',
                'Invalid or expired OAuth state. Please try signing in again.'
            );
        }

        $code = $request->input('code');
        if (empty($code)) {
            return redirect()->route('login')->with(
                'error',
                'Google did not provide an authorization code.'
            );
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');

        if (empty($clientId) || empty($clientSecret) || empty($redirectUri)) {
            return redirect()->route('login')->with(
                'error',
                'Google OAuth credentials are not configured.'
            );
        }

        // Exchange authorization code for access token
        try {
            $tokenResponse = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);
        } catch (\Throwable $e) {
            Log::error('Google OAuth token request exception', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with(
                'error',
                'Failed to connect to Google authentication service. Please try again.'
            );
        }

        if (! $tokenResponse->successful()) {
            Log::error('Google OAuth token request failed', ['body' => $tokenResponse->body()]);

            return redirect()->route('login')->with(
                'error',
                'Failed to authenticate with Google. Please try again.'
            );
        }

        $accessToken = $tokenResponse->json('access_token');
        if (empty($accessToken)) {
            return redirect()->route('login')->with(
                'error',
                'No access token received from Google.'
            );
        }

        // Fetch user profile from Google userinfo endpoint
        try {
            $userinfoResponse = Http::withToken($accessToken)
                ->timeout(15)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');
        } catch (\Throwable $e) {
            Log::error('Google OAuth userinfo request exception', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with(
                'error',
                'Failed to fetch user profile from Google.'
            );
        }

        if (! $userinfoResponse->successful()) {
            return redirect()->route('login')->with(
                'error',
                'Failed to fetch user profile from Google.'
            );
        }

        $googleData = $userinfoResponse->json();
        $googleId = $googleData['sub'] ?? null;
        $email = $googleData['email'] ?? null;
        $name = $googleData['name'] ?? ($googleData['given_name'] ?? 'Google User');
        $avatar = $googleData['picture'] ?? null;

        if (empty($googleId) || empty($email)) {
            return redirect()->route('login')->with(
                'error',
                'Unable to retrieve email or identity from Google.'
            );
        }

        // 1. Check if this Google account is already linked in user_providers
        $userProvider = UserProvider::where('provider', 'google')
            ->where('provider_id', (string) $googleId)
            ->first();

        if ($userProvider) {
            $user = $userProvider->user;
            if ($avatar && $userProvider->avatar !== $avatar) {
                $userProvider->update(['avatar' => $avatar]);
            }
        } else {
            // 2. Check if a user with this email already exists
            $user = User::where('email', $email)->first();

            if (! $user) {
                // Generate a unique username based on the email prefix
                $emailPrefix = explode('@', $email)[0];
                $baseUsername = Str::slug($emailPrefix, '_');
                if (empty($baseUsername)) {
                    $baseUsername = 'user';
                }

                $username = substr($baseUsername, 0, 50);
                $candidate = $username;
                $counter = 1;
                while (User::where('username', $candidate)->exists()) {
                    $candidate = substr($username, 0, 45).'_'.$counter++;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $candidate,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'role' => UserRole::User,
                ]);

                $user->email_verified_at = now();
                $user->save();
            }

            // Link the Google provider
            UserProvider::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_id' => (string) $googleId,
                'avatar' => $avatar,
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($user->role === UserRole::Admin) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome, '.$user->name.'!');
        }

        return redirect()->intended(route('user.dashboard'))->with('success', 'Welcome, '.$user->name.'!');
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
