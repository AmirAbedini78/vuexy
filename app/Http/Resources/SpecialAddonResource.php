<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecialAddonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'number' => $this->number,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
