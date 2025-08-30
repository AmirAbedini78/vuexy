<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\IndividualUser;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            DB::raw("'individual' as provider_type"),
            DB::raw("'Live' as status"),
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
            DB::raw("'company' as provider_type"),
            DB::raw("'Live' as status"),
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
     * Update provider status
     */
    public function updateProviderStatus(Request $request, $id, $type)
    {
        $request->validate([
            'status' => 'required|in:live,denied'
        ]);

        if ($type === 'individual') {
            $provider = IndividualUser::findOrFail($id);
        } else {
            $provider = CompanyUser::findOrFail($id);
        }

        // Since there's no status field in the tables, we'll add a note or use want_to_be_listed
        $provider->update([
            'want_to_be_listed' => $request->status === 'live' ? 'yes' : 'no'
        ]);

        return response()->json([
            'message' => 'Provider status updated successfully',
            'provider' => $provider
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