<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Http\Resources\PeriodResource;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index($listingId)
    {
        $items = Period::where('listing_id', $listingId)->orderBy('order')->get();
        return PeriodResource::collection($items);
    }

    public function store(Request $request, $listingId)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0',
            'min_capacity' => 'nullable|integer|min:0',
            'max_capacity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        $data['listing_id'] = $listingId;
        $item = Period::create($data);
        return new PeriodResource($item);
    }

    public function show($listingId, $id)
    {
        $item = Period::where('listing_id', $listingId)->findOrFail($id);
        return new PeriodResource($item);
    }

    public function update(Request $request, $listingId, $id)
    {
        $item = Period::where('listing_id', $listingId)->findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0',
            'min_capacity' => 'nullable|integer|min:0',
            'max_capacity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        $item->update($data);
        return new PeriodResource($item);
    }

    public function destroy($listingId, $id)
    {
        $item = Period::where('listing_id', $listingId)->findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Period deleted successfully']);
    }

    /**
     * Update multiple periods at once
     */
    public function updateMultiple(Request $request, $listingId)
    {
        $data = $request->validate([
            'periods' => 'required|array',
            'periods.*.title' => 'required|string|max:255',
            'periods.*.start_date' => 'required|date',
            'periods.*.end_date' => 'required|date|after_or_equal:periods.*.start_date',
            'periods.*.price' => 'nullable|numeric|min:0',
            'periods.*.min_capacity' => 'nullable|integer|min:0',
            'periods.*.max_capacity' => 'nullable|integer|min:0',
            'periods.*.is_active' => 'nullable|boolean',
            'periods.*.order' => 'nullable|integer|min:0',
        ]);

        // Delete existing periods for this listing
        Period::where('listing_id', $listingId)->delete();

        // Create new periods
        $periods = [];
        foreach ($data['periods'] as $index => $periodData) {
            $periodData['listing_id'] = $listingId;
            $periodData['order'] = $periodData['order'] ?? $index;
            $periods[] = Period::create($periodData);
        }

        return PeriodResource::collection($periods);
    }
}
