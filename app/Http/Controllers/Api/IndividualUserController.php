<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IndividualUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class IndividualUserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:individual_users,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'profile_image' => 'nullable|file|image|max:2048',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'professional_title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'bio' => 'required|string',
            'website' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:255',
            'country_region_operation' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Handle file uploads
            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('individual/profiles', 'public');
            }

            if ($request->hasFile('id_document')) {
                $data['id_document'] = $request->file('id_document')->store('individual/documents', 'public');
            }

            $individualUser = IndividualUser::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Individual user registered successfully',
                'data' => $individualUser
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $individualUser = IndividualUser::find($id);
        
        if (!$individualUser) {
            return response()->json([
                'success' => false,
                'message' => 'Individual user not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $individualUser
        ]);
    }
} 
