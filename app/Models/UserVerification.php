<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'email',
        'email_verified',
        'email_token',
        'whatsapp_verified',
        'whatsapp_number',
        'whatsapp_code',
        'linkedin_verified',
        'linkedin_id',
        'profile_completed',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'whatsapp_verified' => 'boolean',
        'linkedin_verified' => 'boolean',
        'profile_completed' => 'boolean',
    ];
} 
