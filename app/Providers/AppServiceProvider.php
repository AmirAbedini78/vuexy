<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

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
        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = rtrim(config('app.frontend_url'), '/');
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                true
            );
            $parsed = parse_url($signedUrl);
            $path = $parsed['path'];
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $frontendPath = str_replace('/api/email/verify', '/verify-email', $path);
            return $frontendUrl . $frontendPath . $query;
        });
    }
}
