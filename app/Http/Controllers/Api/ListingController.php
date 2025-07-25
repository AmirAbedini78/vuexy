<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Http\Resources\ListingResource;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        return ListingResource::collection(Listing::with(['itineraries', 'specialAddons'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'listing_type' => 'nullable|string',
            'starting_date' => 'nullable|date',
            'finishing_date' => 'nullable|date',
            'listing_title' => 'nullable|string',
            'listing_description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'min_capacity' => 'nullable|integer',
            'max_capacity' => 'nullable|integer',
            'subtitle' => 'nullable|string',
            'experience_level' => 'nullable|string',
            'fitness_level' => 'nullable|string',
            'activities_included' => 'nullable|string',
            'group_language' => 'nullable|string',
            'maps_and_routes' => 'nullable|array',
            'locations' => 'nullable|string',
            'listing_media' => 'nullable|array',
            'promotional_video' => 'nullable|array',
            'whats_included' => 'nullable|string',
            'whats_not_included' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'providers_faq' => 'nullable|string',
            'personal_policies' => 'nullable|string',
            'personal_policies_text' => 'nullable|string',
            'terms_accepted' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);
        // تبدیل آرایه‌ها به json
        foreach(['maps_and_routes','listing_media','promotional_video'] as $jsonField) {
            if(isset($data[$jsonField])) {
                $data[$jsonField] = json_encode($data[$jsonField]);
            }
        }
        $listing = Listing::create($data);
        return new ListingResource($listing->load(['itineraries', 'specialAddons']));
    }

    public function show($id)
    {
        $listing = Listing::with(['itineraries', 'specialAddons'])->findOrFail($id);
        return new ListingResource($listing);
    }

    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $data = $request->validate([
            'listing_type' => 'nullable|string',
            'starting_date' => 'nullable|date',
            'finishing_date' => 'nullable|date',
            'listing_title' => 'nullable|string',
            'listing_description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'min_capacity' => 'nullable|integer',
            'max_capacity' => 'nullable|integer',
            'subtitle' => 'nullable|string',
            'experience_level' => 'nullable|string',
            'fitness_level' => 'nullable|string',
            'activities_included' => 'nullable|string',
            'group_language' => 'nullable|string',
            'maps_and_routes' => 'nullable|array',
            'locations' => 'nullable|string',
            'listing_media' => 'nullable|array',
            'promotional_video' => 'nullable|array',
            'whats_included' => 'nullable|string',
            'whats_not_included' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'providers_faq' => 'nullable|string',
            'personal_policies' => 'nullable|string',
            'personal_policies_text' => 'nullable|string',
            'terms_accepted' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);
        foreach(['maps_and_routes','listing_media','promotional_video'] as $jsonField) {
            if(isset($data[$jsonField])) {
                $data[$jsonField] = json_encode($data[$jsonField]);
            }
        }
        $listing->update($data);
        return new ListingResource($listing->fresh(['itineraries', 'specialAddons']));
    }

    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();
        return response()->json(['message' => 'Listing deleted successfully']);
    }
}
