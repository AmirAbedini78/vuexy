<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoSaveListing;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AutoSaveListingController extends Controller
{
    /**
     * Get user's auto-save listings
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $autoSaveListings = AutoSaveListing::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $autoSaveListings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch auto-save listings',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save or update auto-save listing
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'listing_type' => 'required|string|in:single-date,multi-date,open-date',
                'form_data' => 'required|array',
                'adventure_title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['status'] = 'on_process';

            // Check if auto-save listing already exists for this user and listing type
            $existingAutoSave = AutoSaveListing::where('user_id', $user->id)
                ->where('listing_type', $data['listing_type'])
                ->first();

            if ($existingAutoSave) {
                // Update existing auto-save
                $existingAutoSave->update($data);
                $autoSaveListing = $existingAutoSave;
            } else {
                // Create new auto-save
                $autoSaveListing = AutoSaveListing::create($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Auto-save data saved successfully',
                'data' => $autoSaveListing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save auto-save data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific auto-save listing
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $autoSaveListing = AutoSaveListing::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$autoSaveListing) {
                return response()->json([
                    'success' => false,
                    'error' => 'Auto-save listing not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $autoSaveListing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch auto-save listing',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update auto-save listing
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $autoSaveListing = AutoSaveListing::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$autoSaveListing) {
                return response()->json([
                    'success' => false,
                    'error' => 'Auto-save listing not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'form_data' => 'sometimes|array',
                'adventure_title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $autoSaveListing->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Auto-save data updated successfully',
                'data' => $autoSaveListing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update auto-save data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete auto-save listing
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $autoSaveListing = AutoSaveListing::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$autoSaveListing) {
                return response()->json([
                    'success' => false,
                    'error' => 'Auto-save listing not found'
                ], 404);
            }

            $autoSaveListing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Auto-save listing deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete auto-save listing',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert auto-save listing to regular listing
     */
    public function convertToListing(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $autoSaveListing = AutoSaveListing::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$autoSaveListing) {
                return response()->json([
                    'success' => false,
                    'error' => 'Auto-save listing not found'
                ], 404);
            }

            // Convert to regular listing
            $listingData = $autoSaveListing->toListingData();
            $listing = Listing::create($listingData);

            // Delete the auto-save listing
            $autoSaveListing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Auto-save listing converted to regular listing successfully',
                'data' => $listing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to convert auto-save listing',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
