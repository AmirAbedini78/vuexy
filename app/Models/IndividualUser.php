<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Step 1: Personal Information
        'full_name',
        'nationality',
        'address1',
        'city',
        'state',
        'dob',
        'languages',
        'address2',
        'postal_code',
        'country',
        
        // Step 2: Account Details
        'passport_image',
        'avatar_image',
        'activity_specialization',
        'years_of_experience',
        'emergency_contact_name',
        'want_to_be_listed',
        'short_bio',
        'certifications',
        'country_of_operation',
        'emergency_contact_phone',
        
        // Step 3: Social Links
        'social_media_links',
        'social_proof_links',
        'terms_accepted',
    ];

    protected $casts = [
        'dob' => 'date',
        'languages' => 'array',
        'social_media_links' => 'array',
        'social_proof_links' => 'array',
        'terms_accepted' => 'boolean',
    ];

    /**
     * Get the user that owns this individual profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
