<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
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
    }
}
