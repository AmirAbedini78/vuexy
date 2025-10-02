<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpecialAddon;
use App\Http\Resources\SpecialAddonResource;
use Illuminate\Http\Request;

class SpecialAddonController extends Controller
{
    public function index($listingId)
    {
        $items = SpecialAddon::where('listing_id', $listingId)->orderBy('order')->get();
        return SpecialAddonResource::collection($items);
    }

    public function store(Request $request, $listingId)
    {
        $data = $request->validate([
            'number' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);
        $data['listing_id'] = $listingId;
        $item = SpecialAddon::create($data);
        return new SpecialAddonResource($item);
    }

    public function show($listingId, $id)
    {
        $item = SpecialAddon::where('listing_id', $listingId)->findOrFail($id);
        return new SpecialAddonResource($item);
    }

    public function update(Request $request, $listingId, $id)
    {
        $item = SpecialAddon::where('listing_id', $listingId)->findOrFail($id);
        $data = $request->validate([
            'number' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);
        $item->update($data);
        return new SpecialAddonResource($item);
    }

    public function destroy($listingId, $id)
    {
        $item = SpecialAddon::where('listing_id', $listingId)->findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'SpecialAddon deleted successfully']);
    }

    /**
     * Update multiple special addons at once
     */
    public function updateMultiple(Request $request, $listingId)
    {
        $data = $request->validate([
            'special_addons' => 'required|array',
            'special_addons.*.number' => 'nullable|string',
            'special_addons.*.title' => 'nullable|string',
            'special_addons.*.description' => 'nullable|string',
            'special_addons.*.price' => 'nullable|numeric',
            'special_addons.*.is_active' => 'nullable|boolean',
            'special_addons.*.order' => 'nullable|integer',
        ]);

        // Delete existing special addons for this listing
        SpecialAddon::where('listing_id', $listingId)->delete();

        // Create new special addons
        $specialAddons = [];
        foreach ($data['special_addons'] as $index => $addonData) {
            $addonData['listing_id'] = $listingId;
            $addonData['order'] = $addonData['order'] ?? $index;
            $specialAddons[] = SpecialAddon::create($addonData);
        }

        return SpecialAddonResource::collection($specialAddons);
    }
}
