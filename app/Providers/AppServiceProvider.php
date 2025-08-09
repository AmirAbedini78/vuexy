<?php

namespace App\Providers;

use App\Models\VerificationToken;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\LinkedIn\Provider;

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
        // Configure LinkedIn Socialite provider
        Socialite::extend('linkedin', function ($app) {
            $config = $app['config']['services.linkedin'];
            return Socialite::buildProvider(Provider::class, $config);
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = rtrim(config('app.frontend_url'), '/');
            VerificationToken::where('user_id', $notifiable->id)->delete();
            $token = Str::random(64);
            $expiresAt = now()->setTimezone(config('app.timezone'))->addMinutes(config('auth.verification.expire', 1440));
            VerificationToken::create([
                'user_id' => $notifiable->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
            $url = $frontendUrl . '/verify/' . $token;
            \Log::info('Verification URL generated', [
                'url' => $url,
                'expires_at' => $expiresAt->toDateTimeString(),
                'timezone' => config('app.timezone'),
            ]);
            return $url;
        });
    }
}
