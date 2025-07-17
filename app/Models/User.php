<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

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
        'password',
        'role',
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
        $url = config('app.frontend_url') . "/reset-password/$token?email=" . urlencode($this->email);
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
        $link = $frontendUrl . '/verify/' . $token;
        $this->notify(new \App\Notifications\VerifyEmail($link));
    }
}
