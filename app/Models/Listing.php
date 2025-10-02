<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'listing_type',
        'starting_date',
        'finishing_date',
        'listing_title',
        'listing_description',
        'price',
        'min_capacity',
        'max_capacity',
        'subtitle',
        'experience_level',
        'fitness_level',
        'activities_included',
        'group_language',
        'maps_and_routes',
        'locations',
        'listing_media',
        'promotional_video',
        'whats_included',
        'whats_not_included',
        'additional_notes',
        'providers_faq',
        'personal_policies',
        'personal_policies_text',
        'terms_accepted',
        'status',
    ];

    public function itineraries()
    {
        return $this->hasMany(ItineraryAccommodation::class);
    }

    public function specialAddons()
    {
        return $this->hasMany(SpecialAddon::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
