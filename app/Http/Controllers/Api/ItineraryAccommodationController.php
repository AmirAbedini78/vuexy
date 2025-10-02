<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItineraryAccommodation;
use App\Http\Resources\ItineraryAccommodationResource;
use Illuminate\Http\Request;

class ItineraryAccommodationController extends Controller
{
    public function index($listingId)
    {
        $items = ItineraryAccommodation::where('listing_id', $listingId)->orderBy('order')->get();
        return ItineraryAccommodationResource::collection($items);
    }

    public function store(Request $request, $listingId)
    {
        $data = $request->validate([
            'day_number' => 'required|integer',
            'title' => 'nullable|string',
            'accommodation' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);
        $data['listing_id'] = $listingId;
        $item = ItineraryAccommodation::create($data);
        return new ItineraryAccommodationResource($item);
    }

    public function show($listingId, $id)
    {
        $item = ItineraryAccommodation::where('listing_id', $listingId)->findOrFail($id);
        return new ItineraryAccommodationResource($item);
    }

    public function update(Request $request, $listingId, $id)
    {
        $item = ItineraryAccommodation::where('listing_id', $listingId)->findOrFail($id);
        $data = $request->validate([
            'day_number' => 'nullable|integer',
            'title' => 'nullable|string',
            'accommodation' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);
        $item->update($data);
        return new ItineraryAccommodationResource($item);
    }

    public function destroy($listingId, $id)
    {
        $item = ItineraryAccommodation::where('listing_id', $listingId)->findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'ItineraryAccommodation deleted successfully']);
    }

    /**
     * Update multiple itineraries at once
     */
    public function updateMultiple(Request $request, $listingId)
    {
        $data = $request->validate([
            'itineraries' => 'required|array',
            'itineraries.*.day_number' => 'required|integer',
            'itineraries.*.title' => 'nullable|string',
            'itineraries.*.accommodation' => 'nullable|string',
            'itineraries.*.location' => 'nullable|string',
            'itineraries.*.description' => 'nullable|string',
            'itineraries.*.link' => 'nullable|string',
            'itineraries.*.order' => 'nullable|integer',
        ]);

        // Delete existing itineraries for this listing
        ItineraryAccommodation::where('listing_id', $listingId)->delete();

        // Create new itineraries
        $itineraries = [];
        foreach ($data['itineraries'] as $index => $itineraryData) {
            $itineraryData['listing_id'] = $listingId;
            $itineraryData['order'] = $itineraryData['order'] ?? $index;
            $itineraries[] = ItineraryAccommodation::create($itineraryData);
        }

        return ItineraryAccommodationResource::collection($itineraries);
    }
}
