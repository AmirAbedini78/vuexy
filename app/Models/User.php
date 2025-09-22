<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'status',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        Log::info('Sending password reset notification', [
            'user_id' => $this->id,
            'email' => $this->email,
            'token' => $token,
        ]);
        
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $url = $frontendUrl . "/reset-password/{$token}?email=" . urlencode($this->email);
        
        Log::info('Password reset URL generated', ['url' => $url]);
        
        $this->notify(new \App\Notifications\ResetPassword($token, $url));
    }

    public function sendEmailVerificationNotification()
    {
        VerificationToken::where('user_id', $this->id)->delete();
        $token = Str::random(64);
        VerificationToken::create([
            'user_id' => $this->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(60),
        ]);
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        
        // Determine user type for proper timeline redirect
        $userType = $this->role === 'user' ? 'individual' : ($this->role === 'company' ? 'company' : 'individual');
        $redirectUrl = "/registration/timeline/{$userType}/{$this->id}";
        
        // Add redirect parameter to verification link
        $link = $frontendUrl . '/verify/' . $token . '?redirect=' . urlencode($redirectUrl);
        
        Log::info('Email verification link generated', [
            'user_id' => $this->id,
            'user_type' => $userType,
            'redirect_url' => $redirectUrl,
            'encoded_redirect' => urlencode($redirectUrl),
            'final_link' => $link
        ]);
        
        $this->notify(new \App\Notifications\VerifyEmail($link));
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the individual user profile
     */
    public function individualUser()
    {
        return $this->hasOne(IndividualUser::class);
    }

    /**
     * Get the company user profile
     */
    public function companyUser()
    {
        return $this->hasOne(CompanyUser::class);
    }
}
