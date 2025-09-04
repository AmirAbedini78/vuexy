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
            // Auth & linkage
            'user_id' => 'nullable|exists:users,id',
            // Step 1: Personal Information
            'full_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'dob' => 'nullable|string|max:255', // Changed to nullable
            'languages' => 'nullable|string', // Changed from array to string (JSON)
            'address2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            
            // Step 2: Account Details
            'passport_image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:5120', // Fixed image validation
            'avatar_image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:3072', // Fixed image validation
            'activity_specialization' => 'required|string|max:255',
            'years_of_experience' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'want_to_be_listed' => 'required|string|in:yes,no,unsure',
            'short_bio' => 'required|string',
            'certifications' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'country_of_operation' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            
            // Step 3: Social Links
            'social_media_links' => 'nullable|string', // Changed from array to string (JSON)
            'social_proof_links' => 'nullable|string', // Changed from array to string (JSON)
            'terms_accepted' => 'required|string|in:1,0', // Changed from boolean to string
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

            // Ensure user_id is set and linked to the authenticated user when available
            $authUserId = optional($request->user())->id;
            $data['user_id'] = $authUserId ?? ($data['user_id'] ?? $request->input('user_id'));
            if (empty($data['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required to create provider profile.'
                ], 422);
            }

            // Handle file uploads
            if ($request->hasFile('passport_image') && $request->file('passport_image')->isValid()) {
                $data['passport_image'] = $request->file('passport_image')->store('individual/passport', 'public');
            }

            if ($request->hasFile('avatar_image') && $request->file('avatar_image')->isValid()) {
                $data['avatar_image'] = $request->file('avatar_image')->store('individual/avatar', 'public');
            }

            if ($request->hasFile('certifications') && $request->file('certifications')->isValid()) {
                $data['certifications'] = $request->file('certifications')->store('individual/certifications', 'public');
            }

            // Convert JSON strings to arrays
            if (isset($data['languages']) && is_string($data['languages'])) {
                $data['languages'] = json_decode($data['languages'], true) ?: [];
            }

            if (isset($data['social_media_links']) && is_string($data['social_media_links'])) {
                $socialMediaLinks = json_decode($data['social_media_links'], true) ?: [];
                $data['social_media_links'] = array_filter($socialMediaLinks, function($link) {
                    return !empty($link) && $link !== '';
                });
            } else {
                $data['social_media_links'] = [];
            }

            if (isset($data['social_proof_links']) && is_string($data['social_proof_links'])) {
                $socialProofLinks = json_decode($data['social_proof_links'], true) ?: [];
                $data['social_proof_links'] = array_filter($socialProofLinks, function($link) {
                    return !empty($link) && $link !== '';
                });
            } else {
                $data['social_proof_links'] = [];
            }

            // Convert terms_accepted to boolean
            $data['terms_accepted'] = $data['terms_accepted'] === '1';

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
