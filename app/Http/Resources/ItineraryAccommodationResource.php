<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryAccommodationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'day_number' => $this->day_number,
            'title' => $this->title,
            'accommodation' => $this->accommodation,
            'location' => $this->location,
            'description' => $this->description,
            'link' => $this->link,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
