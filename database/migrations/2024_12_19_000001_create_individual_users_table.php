<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('individual_users', function (Blueprint $table) {
            $table->id();
            // Step 1: Personal Information
            $table->string('full_name');
            $table->string('nationality');
            $table->string('address1');
            $table->string('city');
            $table->string('state');
            $table->date('dob');
            $table->json('languages')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code');
            $table->string('country');
            
            // Step 2: Account Details
            $table->string('passport_image')->nullable();
            $table->string('avatar_image')->nullable();
            $table->string('activity_specialization');
            $table->string('years_of_experience');
            $table->string('emergency_contact_name');
            $table->string('want_to_be_listed');
            $table->text('short_bio');
            $table->string('certifications')->nullable();
            $table->string('country_of_operation');
            $table->string('emergency_contact_phone');
            
            // Step 3: Social Links
            $table->json('social_media_links')->nullable();
            $table->json('social_proof_links')->nullable();
            $table->boolean('terms_accepted')->default(false);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('individual_users');
    }
}; 
