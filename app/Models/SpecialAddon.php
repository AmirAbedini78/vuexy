<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialAddon extends Model
{
    protected $fillable = [
        'listing_id',
        'number',
        'title',
        'description',
        'price',
        'is_active',
        'order',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
