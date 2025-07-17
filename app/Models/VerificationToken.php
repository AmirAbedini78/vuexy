<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if token is expired using proper timezone comparison
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        // Get current time in the same timezone as the expires_at field
        $now = now()->setTimezone($this->expires_at->timezone);
        
        // Compare times in the same timezone
        return $this->expires_at->lt($now);
    }
}
