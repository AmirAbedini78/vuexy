<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CompanyUserController extends Controller
{
    public function store(Request $request)
    {
        // Debug: Log the incoming data
        \Log::info('Company User Registration Data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            // Step 1: Company Information
            'company_name' => 'required|string|max:255',
            'vat_id' => 'nullable|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'country_of_registration' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255', // Made nullable
            
            // Step 2: Business Details
            'passport_image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:5120', // Company Logo - Fixed validation
            'avatar_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Business License
            'activity_specialization' => 'required|string|max:255',
            'want_to_be_listed' => 'required|string|in:yes,no,unsure',
            'short_bio' => 'required|string',
            'certifications' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            
            // Step 3: Social Links
            'company_website' => 'nullable|string|max:255', // Removed url validation to be more flexible
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

            // Handle file uploads
            if ($request->hasFile('passport_image') && $request->file('passport_image')->isValid()) {
                $data['passport_image'] = $request->file('passport_image')->store('company/logo', 'public');
            }

            if ($request->hasFile('avatar_image') && $request->file('avatar_image')->isValid()) {
                $data['avatar_image'] = $request->file('avatar_image')->store('company/license', 'public');
            }

            if ($request->hasFile('certifications') && $request->file('certifications')->isValid()) {
                $data['certifications'] = $request->file('certifications')->store('company/certifications', 'public');
            }

            // Filter out empty social media links
            if (isset($data['social_media_links']) && is_string($data['social_media_links'])) {
                $socialMediaLinks = json_decode($data['social_media_links'], true) ?: [];
                $data['social_media_links'] = array_filter($socialMediaLinks, function($link) {
                    return !empty($link) && $link !== '';
                });
            } else {
                $data['social_media_links'] = [];
            }

            // Filter out empty social proof links
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

            $companyUser = CompanyUser::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Company user registered successfully',
                'data' => $companyUser
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
        $companyUser = CompanyUser::find($id);
        
        if (!$companyUser) {
            return response()->json([
                'success' => false,
                'message' => 'Company user not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $companyUser
        ]);
    }
} 
