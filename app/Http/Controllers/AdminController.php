<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\IndividualUser;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'recent_registrations' => User::where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'name', 'email', 'created_at']),
            'recent_listings' => Listing::orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'title', 'created_at']),
        ];

        return response()->json($stats);
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
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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
