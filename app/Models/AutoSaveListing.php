<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoSaveListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_type',
        'status',
        'form_data',
        'packages',
        'special_addons',
        'itinerary',
        'periods',
        'adventure_title',
        'description',
        'location',
        'price',
        'min_capacity',
        'max_capacity',
        'group_language',
        'experience_level',
        'fitness_level',
        'activities_included',
        'maps_and_routes',
        'listing_media',
        'promotional_video',
        'whats_included',
        'whats_not_included',
        'additional_notes',
        'providers_faq',
        'personal_policies',
        'starting_date',
        'finishing_date',
        'min_advance_notice',
        'max_advance_booking',
        'available_days',
    ];

    protected $casts = [
        'form_data' => 'array',
        'packages' => 'array',
        'special_addons' => 'array',
        'itinerary' => 'array',
        'periods' => 'array',
        'activities_included' => 'array',
        'maps_and_routes' => 'array',
        'listing_media' => 'array',
        'promotional_video' => 'array',
        'available_days' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the auto-save listing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert auto-save listing to regular listing data
     */
    public function toListingData(): array
    {
        return [
            'user_id' => $this->user_id,
            'listing_type' => $this->listing_type,
            'listing_title' => $this->adventure_title,
            'description' => $this->description,
            'locations' => $this->location,
            'price' => $this->price,
            'min_capacity' => $this->min_capacity,
            'max_capacity' => $this->max_capacity,
            'group_language' => $this->group_language,
            'experience_level' => $this->experience_level,
            'fitness_level' => $this->fitness_level,
            'activities_included' => $this->activities_included,
            'maps_and_routes' => $this->maps_and_routes,
            'listing_media' => $this->listing_media,
            'promotional_video' => $this->promotional_video,
            'whats_included' => $this->whats_included,
            'whats_not_included' => $this->whats_not_included,
            'additional_notes' => $this->additional_notes,
            'providers_faq' => $this->providers_faq,
            'personal_policies' => $this->personal_policies,
            'starting_date' => $this->starting_date,
            'finishing_date' => $this->finishing_date,
            'min_advance_notice' => $this->min_advance_notice,
            'max_advance_booking' => $this->max_advance_booking,
            'available_days' => $this->available_days,
            'status' => 'submitted',
        ];
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'on_process' => 'On Process',
            'submitted' => 'Submitted',
            'draft' => 'Draft',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'on_process' => 'orange',
            'submitted' => 'green',
            'draft' => 'grey',
            default => 'grey'
        };
    }
}
