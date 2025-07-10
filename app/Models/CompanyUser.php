<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'business_type',
        'industry',
        'registration_number',
        'tax_id',
        'website',
        'phone',
        'email',
        'country',
        'city',
        'address',
        'postal_code',
        'logo',
        'business_license',
        'professional_title',
        'bio',
        'linkedin',
        'twitter',
        'instagram',
        'facebook',
        'youtube',
        'tiktok',
    ];
} 
