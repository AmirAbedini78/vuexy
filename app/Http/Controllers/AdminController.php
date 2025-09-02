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
            // Map want_to_be_listed => status: yes->active, unsure/null->approved, no->rejected
            $listed = isset($provider->want_to_be_listed) ? strtolower($provider->want_to_be_listed) : null;
            if ($listed === 'yes') {
                $provider->status = 'active';
            } elseif ($listed === 'no') {
                $provider->status = 'rejected';
            } else {
                $provider->status = 'approved'; // default
            }
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
            'status' => 'required|in:active,approved,rejected'
        ]);

        if ($type === 'individual') {
            $provider = IndividualUser::findOrFail($id);
            $userId = $provider->user_id;
        } else {
            $provider = CompanyUser::findOrFail($id);
            $userId = $provider->user_id;
        }

        // Map status to want_to_be_listed: active->yes, approved->unsure, rejected->no
        $map = [
            'active' => 'yes',
            'approved' => 'unsure',
            'rejected' => 'no',
        ];

        $provider->update([
            'want_to_be_listed' => $map[$request->status],
        ]);

        // Notify user via email & database
        $user = User::find($userId);
        if ($user) {
            $providerName = $type === 'individual' ? ($provider->full_name ?? null) : ($provider->company_name ?? null);
            $user->notify(new ProviderStatusUpdated($request->status, $type, $providerName));
        }

        return response()->json([
            'success' => true,
            'message' => 'Provider status updated successfully',
            'provider' => $provider,
        ]);
    }

    /**
     * Get all users for admin management
     */
    public function users(Request $request)
    {
        $query = User::with(['individualUser', 'companyUser']);

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

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
                  ->orWhere('listing_description', 'like', "%{$search}%");
            });
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($listings);
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
