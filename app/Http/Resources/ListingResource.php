<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'listing_type' => $this->listing_type,
            'starting_date' => $this->starting_date,
            'finishing_date' => $this->finishing_date,
            'listing_title' => $this->listing_title,
            'listing_description' => $this->listing_description,
            'price' => $this->price,
            'min_capacity' => $this->min_capacity,
            'max_capacity' => $this->max_capacity,
            'subtitle' => $this->subtitle,
            'experience_level' => $this->experience_level,
            'fitness_level' => $this->fitness_level,
            'activities_included' => $this->activities_included,
            'group_language' => $this->group_language,
            'maps_and_routes' => $this->maps_and_routes,
            'locations' => $this->locations,
            'listing_media' => $this->listing_media,
            'promotional_video' => $this->promotional_video,
            'whats_included' => $this->whats_included,
            'whats_not_included' => $this->whats_not_included,
            'additional_notes' => $this->additional_notes,
            'providers_faq' => $this->providers_faq,
            'personal_policies' => $this->personal_policies,
            'personal_policies_text' => $this->personal_policies_text,
            'terms_accepted' => $this->terms_accepted,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'itineraries' => $this->itineraries,
            'special_addons' => $this->specialAddons,
            'packages' => $this->packages,
            'periods' => $this->periods,
        ];
    }
}
