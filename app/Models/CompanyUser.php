<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Step 1: Company Information
        'company_name',
        'vat_id',
        'address1',
        'city',
        'state',
        'contact_person',
        'country_of_registration',
        'address2',
        'postal_code',
        'country',
        'business_type',
        
        // Step 2: Business Details
        'passport_image', // Company Logo
        'avatar_image', // Business License
        'activity_specialization',
        'want_to_be_listed',
        'short_bio',
        'certifications',
        
        // Step 3: Social Links
        'company_website',
        'social_media_links',
        'social_proof_links',
        'terms_accepted',
    ];

    protected $casts = [
        'social_media_links' => 'array',
        'social_proof_links' => 'array',
        'terms_accepted' => 'boolean',
    ];

    /**
     * Get the user that owns this company profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
