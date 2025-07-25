<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryAccommodation extends Model
{
    protected $fillable = [
        'listing_id',
        'day_number',
        'title',
        'accommodation',
        'location',
        'description',
        'link',
        'order',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
