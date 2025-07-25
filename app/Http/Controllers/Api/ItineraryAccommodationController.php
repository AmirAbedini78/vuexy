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
}
