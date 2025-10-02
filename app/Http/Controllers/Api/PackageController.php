<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Http\Resources\PackageResource;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index($listingId)
    {
        $items = Package::where('listing_id', $listingId)->orderBy('order')->get();
        return PackageResource::collection($items);
    }

    public function store(Request $request, $listingId)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $data['listing_id'] = $listingId;
        $item = Package::create($data);
        return new PackageResource($item);
    }

    public function show($listingId, $id)
    {
        $item = Package::where('listing_id', $listingId)->findOrFail($id);
        return new PackageResource($item);
    }

    public function update(Request $request, $listingId, $id)
    {
        $item = Package::where('listing_id', $listingId)->findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $item->update($data);
        return new PackageResource($item);
    }

    public function destroy($listingId, $id)
    {
        $item = Package::where('listing_id', $listingId)->findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Package deleted successfully']);
    }

    /**
     * Update multiple packages at once
     */
    public function updateMultiple(Request $request, $listingId)
    {
        $data = $request->validate([
            'packages' => 'required|array',
            'packages.*.title' => 'required|string|max:255',
            'packages.*.description' => 'nullable|string',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.duration' => 'nullable|string|max:255',
            'packages.*.capacity' => 'nullable|integer|min:0',
            'packages.*.is_active' => 'nullable|boolean',
            'packages.*.order' => 'nullable|integer|min:0',
        ]);

        // Delete existing packages for this listing
        Package::where('listing_id', $listingId)->delete();

        // Create new packages
        $packages = [];
        foreach ($data['packages'] as $index => $packageData) {
            $packageData['listing_id'] = $listingId;
            $packageData['order'] = $packageData['order'] ?? $index;
            $packages[] = Package::create($packageData);
        }

        return PackageResource::collection($packages);
    }
}
