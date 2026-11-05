<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // Configure branded password reset URL and email representation
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);
        });

        ResetPassword::toMailUsing(function (User $user, string $token) {
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Reset Your AptiMedha Password')
                ->greeting("Hello {$user->name},")
                ->line('You are receiving this email because we received a password reset request for your AptiMedha account.')
                ->action('Reset Password', $resetUrl)
                ->line('This password reset link will expire in ' . config('auth.passwords.users.expire', 60) . ' minutes.')
                ->line('If you did not request a password reset, no further action is required; your account remains secure.');
        });

        // Register layout views as components
        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.admin', 'layouts.admin');
        Blade::component('layouts.user', 'layouts.user');

        // Configure Abuse Protection Rate Limiters (Phase 13.2)
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiters for abuse protection.
     */
    protected function configureRateLimiting(): void
    {
        // Registration: 5 attempts per minute per IP
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) {
                $message = 'Too many registration attempts. Please wait a minute before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Contact Us: 5 submissions per minute per IP
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) {
                $message = 'Too many contact submissions. Please wait a minute before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Discussions: 10 submissions per minute per authenticated user
        RateLimiter::for('discussions', function (Request $request) {
            return Limit::perMinute(10)->by((string) ($request->user()?->id ?: $request->ip()))->response(function (Request $request, array $headers) {
                $message = 'Too many discussion comments posted. Please wait a minute before submitting again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Password Reset Request: 5 requests per minute per IP
        RateLimiter::for('password-email', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function (Request $request, array $headers) {
                $message = 'Too many password reset requests. Please wait a minute before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Password Reset Execution: 10 requests per minute per IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function (Request $request, array $headers) {
                $message = 'Too many password reset attempts. Please wait a minute before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Question Reports: 10 submissions per minute per authenticated user
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perMinute(10)->by((string) ($request->user()?->id ?: $request->ip()))->response(function (Request $request, array $headers) {
                $message = 'Too many question reports submitted. Please wait a minute before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });

        // Bookmark toggling: 30 requests per minute per authenticated user
        RateLimiter::for('bookmarks', function (Request $request) {
            return Limit::perMinute(30)->by((string) ($request->user()?->id ?: $request->ip()))->response(function (Request $request, array $headers) {
                $message = 'Too many bookmark requests. Please wait a moment before trying again.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 429, $headers);
                }
                return response($message, 429, $headers);
            });
        });
    }
}
