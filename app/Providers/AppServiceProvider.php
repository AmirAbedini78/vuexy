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
            $frontendUrl = rtrim(Config::get('app.frontend_url', 'http://vuexy.test'), '/');
            $url = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
            $parsed = parse_url($url);
            $path = $parsed['path'];
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $pathParts = explode('/', trim($path, '/'));
            // Find the id and hash in the path, regardless of any prefix like 'build'
            $id = $pathParts[count($pathParts) - 2];
            $hash = $pathParts[count($pathParts) - 1];
            $verifyPath = "/verify-email/$id/$hash";
            return $frontendUrl . $verifyPath . $query;
        });
    }
}
