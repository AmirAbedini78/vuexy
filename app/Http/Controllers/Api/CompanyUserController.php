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
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:company_users,email',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'logo' => 'nullable|file|image|max:2048',
            'business_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'professional_title' => 'required|string|max:255',
            'bio' => 'required|string',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
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
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('company/logos', 'public');
            }

            if ($request->hasFile('business_license')) {
                $data['business_license'] = $request->file('business_license')->store('company/licenses', 'public');
            }

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
