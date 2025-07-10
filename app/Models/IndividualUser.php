<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'nationality',
        'country',
        'city',
        'address',
        'postal_code',
        'profile_image',
        'id_document',
        'professional_title',
        'industry',
        'bio',
        'website',
        'linkedin',
        'twitter',
        'instagram',
        'facebook',
        'youtube',
        'tiktok',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'country_region_operation',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
} 
