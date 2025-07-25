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
}
