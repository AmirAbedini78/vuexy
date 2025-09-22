<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\IndividualUser;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProviderStatusUpdated;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_listings' => Listing::count(),
            'total_individual_users' => IndividualUser::count(),
            'total_company_users' => CompanyUser::count(),
            'total_providers' => IndividualUser::count() + CompanyUser::count(),
            'all_providers' => IndividualUser::count() + CompanyUser::count(),
            'total_bookings' => 345, // Placeholder
            'total_explorer_passports' => 840, // Placeholder
            'total_revenue' => 590000, // Placeholder
            'recent_registrations' => User::where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'name', 'email', 'created_at']),
            'recent_listings' => Listing::orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'listing_title', 'created_at']),
        ];

        return response()->json($stats);
    }

    /**
     * Get all providers for admin management
     */
    public function providers(Request $request)
    {
        Log::info('AdminController::providers called');
        
        $individualUsers = IndividualUser::select([
            'user_id',
            'id',
            'full_name as provider_name',
            'activity_specialization',
            'years_of_experience',
            'country_of_operation',
            'want_to_be_listed',
            'created_at',
            'nationality',
            'address1',
            'city',
            'state',
            'postal_code',
            'country',
            'dob',
            'languages',
            'address2',
            'emergency_contact_name',
            'emergency_contact_phone',
            'short_bio',
            'terms_accepted',
            DB::raw("'individual' as provider_type"),
            DB::raw("0 as total_listings"),
            DB::raw("0 as total_bookings")
        ]);

        $companyUsers = CompanyUser::select([
            'user_id',
            'id',
            'company_name as provider_name',
            'activity_specialization',
            'business_type as years_of_experience',
            'country as country_of_operation',
            'want_to_be_listed',
            'created_at',
            'vat_id',
            'address1',
            'city',
            'state',
            'postal_code',
            'country',
            'contact_person',
            'country_of_registration',
            'address2',
            'short_bio',
            'company_website',
            'terms_accepted',
            DB::raw("'company' as provider_type"),
            DB::raw("0 as total_listings"),
            DB::raw("0 as total_bookings")
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $individualUsers->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('activity_specialization', 'like', "%{$search}%")
                  ->orWhere('country_of_operation', 'like', "%{$search}%");
            });
            
            $companyUsers->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('activity_specialization', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Provider type filter
        $providers = collect();
        
        if (!$request->filled('provider_type') || $request->provider_type === 'all' || $request->provider_type === 'individual') {
            $providers = $providers->concat($individualUsers->get());
        }
        
        if (!$request->filled('provider_type') || $request->provider_type === 'all' || $request->provider_type === 'company') {
            $providers = $providers->concat($companyUsers->get());
        }

        // Derive status from want_to_be_listed
        $providers = $providers->map(function ($provider) {
            // Map want_to_be_listed => status: yes->active, no->rejected, everything else->review
            $listed = isset($provider->want_to_be_listed) ? strtolower(trim($provider->want_to_be_listed)) : null;
            
            // Only set to active if explicitly 'yes'
            if ($listed === 'yes') {
                $provider->status = 'active';
            } 
            // Only set to rejected if explicitly 'no'
            elseif ($listed === 'no') {
                $provider->status = 'rejected';
            } 
            // Default to review for everything else (null, empty, unsure, etc.)
            else {
                $provider->status = 'review';
            }
            
            // Log the status mapping for debugging
            Log::info("Provider status mapping", [
                'provider_id' => $provider->id,
                'want_to_be_listed' => $provider->want_to_be_listed,
                'mapped_status' => $provider->status
            ]);
            
            return $provider;
        });

        // Sort by created_at
        $providers = $providers->sortByDesc('created_at');

        // Manual pagination
        $perPage = 20;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedProviders = $providers->slice($offset, $perPage);
        
        $result = [
            'data' => $paginatedProviders->values(),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $providers->count(),
            'last_page' => ceil($providers->count() / $perPage),
        ];

        Log::info('AdminController::providers result', [
            'total_providers' => $providers->count(),
            'individual_count' => $individualUsers->count(),
            'company_count' => $companyUsers->count(),
            'result_data_count' => count($result['data'])
        ]);

        return response()->json($result);
    }

    /**
     * Get provider details
     */
    public function provider($id, $type)
    {
        if ($type === 'individual') {
            $provider = IndividualUser::findOrFail($id);
        } else {
            $provider = CompanyUser::findOrFail($id);
        }

        return response()->json($provider);
    }

    /**
     * Update provider information
     */
    public function updateProvider(Request $request, $id, $type)
    {
        try {
            Log::info('Update provider request data:', $request->all());
            
            if ($type === 'individual') {
                $provider = IndividualUser::findOrFail($id);
                
                $validatedData = $request->validate([
                    'full_name' => 'nullable|string|max:255',
                    'nationality' => 'nullable|string|max:255',
                    'address1' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'state' => 'nullable|string|max:255',
                    'dob' => 'nullable|date',
                    'languages' => 'nullable|array',
                    'postal_code' => 'nullable|string|max:20',
                    'country' => 'nullable|string|max:255',
                    'activity_specialization' => 'nullable|string|max:255',
                    'years_of_experience' => 'nullable|string|max:255',
                    'emergency_contact_name' => 'nullable|string|max:255',
                    'want_to_be_listed' => 'nullable|string|in:yes,no,unsure',
                    'short_bio' => 'nullable|string',
                    'country_of_operation' => 'nullable|string|max:255',
                    'emergency_contact_phone' => 'nullable|string|max:20',
                    'terms_accepted' => 'nullable|boolean',
                ]);
                
                Log::info('Individual provider validated data:', $validatedData);

                // Filter out null/empty values and update only provided fields
                $updateData = array_filter($validatedData, function($value) {
                    return $value !== null && $value !== '';
                });
                
                Log::info('Individual provider filtered update data:', $updateData);
                
                $provider->update($updateData);
                
            } else {
                $provider = CompanyUser::findOrFail($id);
                
                $validatedData = $request->validate([
                    'company_name' => 'nullable|string|max:255',
                    'vat_id' => 'nullable|string|max:255',
                    'address1' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'state' => 'nullable|string|max:255',
                    'contact_person' => 'nullable|string|max:255',
                    'country_of_registration' => 'nullable|string|max:255',
                    'postal_code' => 'nullable|string|max:20',
                    'country' => 'nullable|string|max:255',
                    'business_type' => 'nullable|string|max:255',
                    'activity_specialization' => 'nullable|string|max:255',
                    'want_to_be_listed' => 'nullable|string|in:yes,no,unsure',
                    'short_bio' => 'nullable|string',
                    'company_website' => 'nullable|string|max:255',
                    'terms_accepted' => 'nullable|boolean',
                ]);
                
                Log::info('Company provider validated data:', $validatedData);

                // Filter out null/empty values and update only provided fields
                $updateData = array_filter($validatedData, function($value) {
                    return $value !== null && $value !== '';
                });
                
                Log::info('Company provider filtered update data:', $updateData);
                
                $provider->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Provider updated successfully',
                'data' => $provider
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating provider: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete provider
     */
    public function deleteProvider($id, $type)
    {
        try {
            if ($type === 'individual') {
                $provider = IndividualUser::findOrFail($id);
            } else {
                $provider = CompanyUser::findOrFail($id);
            }

            $provider->delete();

            return response()->json([
                'success' => true,
                'message' => 'Provider deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting provider: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update provider status
     */
    public function updateProviderStatus(Request $request, $id, $type)
    {
        $request->validate([
            'status' => 'required|in:active,review,rejected'
        ]);

        try {
            if ($type === 'individual') {
                $provider = IndividualUser::findOrFail($id);
                $userId = $provider->user_id;
            } else {
                $provider = CompanyUser::findOrFail($id);
                $userId = $provider->user_id;
            }

            // Map status to want_to_be_listed: active->yes, review->unsure, rejected->no
            $map = [
                'active' => 'yes',
                'review' => 'unsure',
                'rejected' => 'no',
            ];

            // Update the provider status
            $provider->update([
                'want_to_be_listed' => $map[$request->status],
            ]);

            // Add the status field to the response for frontend
            $provider->status = $request->status;

            // Notify user via email & database
            $user = User::find($userId);
            if ($user) {
                $providerName = $type === 'individual' ? ($provider->full_name ?? null) : ($provider->company_name ?? null);
                // Send immediately to avoid relying on queue worker availability
                $user->notifyNow(new ProviderStatusUpdated($request->status, $type, $providerName));
            }

            return response()->json([
                'success' => true,
                'message' => 'Provider status updated successfully',
                'provider' => $provider,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating provider status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update provider status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get provider status for current user
     */
    public function getProviderStatus(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Load both profiles (if exist)
            $individualUser = IndividualUser::where('user_id', $user->id)->first();
            $companyUser = CompanyUser::where('user_id', $user->id)->first();

            if (!$individualUser && !$companyUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'No provider profile found',
                    'status' => 'not_found'
                ]);
            }

            // Determine status across both profiles
            $mapToStatus = function ($listed) {
                $listed = isset($listed) ? strtolower(trim($listed)) : null;
                if ($listed === 'yes') return 'active';
                if ($listed === 'no') return 'rejected';
                return 'review';
            };

            $indStatus = $individualUser ? $mapToStatus($individualUser->want_to_be_listed) : null;
            $comStatus = $companyUser ? $mapToStatus($companyUser->want_to_be_listed) : null;

            // Priority: any 'active' -> active; else if any 'rejected' and none active -> rejected; else review
            $statuses = array_filter([$indStatus, $comStatus]);
            $status = 'review';
            if (in_array('active', $statuses, true)) {
                $status = 'active';
            } elseif (in_array('rejected', $statuses, true)) {
                $status = 'rejected';
            }

            // Choose providerType/id for response (prefer the one that yielded the chosen status)
            $providerType = null;
            $providerId = null;
            if ($individualUser && $mapToStatus($individualUser->want_to_be_listed) === $status) {
                $providerType = 'individual';
                $providerId = $individualUser->id;
            } elseif ($companyUser && $mapToStatus($companyUser->want_to_be_listed) === $status) {
                $providerType = 'company';
                $providerId = $companyUser->id;
            } elseif ($individualUser) {
                $providerType = 'individual';
                $providerId = $individualUser->id;
            } elseif ($companyUser) {
                $providerType = 'company';
                $providerId = $companyUser->id;
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'provider_type' => $providerType,
                'provider_id' => $providerId,
                'individual_want_to_be_listed' => $individualUser->want_to_be_listed ?? null,
                'company_want_to_be_listed' => $companyUser->want_to_be_listed ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting provider status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get provider status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users for admin management
     */
    public function users(Request $request)
    {
        $query = User::with(['individualUser', 'companyUser'])->orderByDesc('created_at');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Status filter (if status field exists)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(20);

        return response()->json($users);
    }

    /**
     * Get user details
     */
    public function user($id)
    {
        $user = User::with(['individualUser', 'companyUser'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $user->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Update user (admin)
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:user,admin',
            'status' => 'sometimes|required|in:active,inactive,suspended',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
            unset($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Delete user (admin)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    /**
     * Create a new user from admin panel
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        $password = $validated['password'] ?? str()->random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $password,
            'status' => 'active',
        ]);

        // Optionally email credentials here (omitted)

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'plain_password' => app()->isLocal() ? $password : null,
        ], 201);
    }

    /**
     * Impersonate a user: returns a Sanctum token for target user
     */
    public function impersonate(Request $request, $id)
    {
        $admin = $request->user();
        if (!$admin || !$admin->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $target = User::findOrFail($id);
        $token = $target->createToken('impersonation')->plainTextToken;

        return response()->json([
            'message' => 'Impersonation token issued',
            'user' => $target,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get all listings for admin management
     */
    public function listings(Request $request)
    {
        $query = Listing::with(['user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('listing_title', 'like', "%{$search}%")
                  ->orWhere('listing_description', 'like', "%{$search}%")
                  ->orWhere('listing_type', 'like', "%{$search}%");
            });
        }

        // Listing type filter
        if ($request->filled('listing_type') && $request->listing_type !== 'all') {
            $query->where('listing_type', $request->listing_type);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($listings);
    }

    /**
     * Get listing details
     */
    public function listing($id)
    {
        $listing = Listing::with(['user', 'itineraries', 'specialAddons'])->findOrFail($id);
        return response()->json($listing);
    }

    /**
     * Update listing information
     */
    public function updateListing(Request $request, $id)
    {
        try {
            Log::info('Update listing request data:', $request->all());
            
            $listing = Listing::findOrFail($id);
            
            $validatedData = $request->validate([
                'listing_title' => 'nullable|string|max:255',
                'listing_description' => 'nullable|string',
                'listing_type' => 'nullable|string|in:single-date,multi-date,open-date,other',
                'starting_date' => 'nullable|date',
                'finishing_date' => 'nullable|date',
                'price' => 'nullable|numeric|min:0',
                'min_capacity' => 'nullable|integer|min:1',
                'max_capacity' => 'nullable|integer|min:1',
                'subtitle' => 'nullable|string|max:255',
                'experience_level' => 'nullable|string|max:255',
                'fitness_level' => 'nullable|string|max:255',
                'activities_included' => 'nullable|string',
                'group_language' => 'nullable|string|max:255',
                'locations' => 'nullable|string|max:255',
                'maps_and_routes' => 'nullable|array',
                'listing_media' => 'nullable|array',
                'promotional_video' => 'nullable|array',
                'whats_included' => 'nullable|string',
                'whats_not_included' => 'nullable|string',
                'additional_notes' => 'nullable|string',
                'providers_faq' => 'nullable|string',
                'personal_policies' => 'nullable|string|max:255',
                'personal_policies_text' => 'nullable|string',
                'status' => 'nullable|string|in:submitted,approved,live,denied,edit_review,other_events,inactive,draft,published,archived',
                'terms_accepted' => 'nullable|boolean',
            ]);
            
            Log::info('Listing validated data:', $validatedData);

            // Filter out null/empty values and update only provided fields
            $updateData = array_filter($validatedData, function($value) {
                return $value !== null && $value !== '';
            });

            // Encode array fields to JSON for storage
            foreach (['maps_and_routes', 'listing_media', 'promotional_video'] as $jsonField) {
                if (array_key_exists($jsonField, $updateData)) {
                    $updateData[$jsonField] = json_encode($updateData[$jsonField]);
                }
            }
            
            Log::info('Listing filtered update data:', $updateData);
            
            // Track old status for notification decision
            $oldStatus = $listing->status;
            $listing->update($updateData);

            // If event-status keys used, normalize to our extended statuses field
            if (array_key_exists('status', $updateData) && $listing->user) {
                try {
                    // Notify the listing owner about status change
                    $newStatus = $listing->status;
                    if ($newStatus !== $oldStatus && in_array($newStatus, ['submitted','approved','live','denied','edit_review','other_events','inactive'])) {
                        // Send immediately so emails are delivered even if queue worker is down
                        $listing->user->notifyNow(new \App\Notifications\ListingStatusUpdated($newStatus, $listing->listing_title));
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed sending listing status notification: '.$e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Listing updated successfully',
                'data' => $listing
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating listing: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete listing
     */
    public function deleteListing($id)
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Listing deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting listing: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete listing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system statistics
     */
    public function statistics()
    {
        $stats = [
            'users_by_month' => User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'listings_by_month' => Listing::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
        ];

        return response()->json($stats);
    }
} 
