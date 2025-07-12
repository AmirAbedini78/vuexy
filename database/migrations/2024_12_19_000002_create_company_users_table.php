<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();
            // Step 1: Company Information
            $table->string('company_name');
            $table->string('vat_id')->nullable();
            $table->string('address1');
            $table->string('city');
            $table->string('state');
            $table->string('contact_person')->nullable();
            $table->string('country_of_registration');
            $table->string('address2')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('business_type');
            
            // Step 2: Business Details
            $table->string('passport_image')->nullable(); // Company Logo
            $table->string('avatar_image')->nullable(); // Business License
            $table->string('activity_specialization');
            $table->string('want_to_be_listed');
            $table->text('short_bio');
            $table->string('certifications')->nullable();
            
            // Step 3: Social Links
            $table->string('company_website')->nullable();
            $table->json('social_media_links')->nullable();
            $table->json('social_proof_links')->nullable();
            $table->boolean('terms_accepted')->default(false);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_users');
    }
}; 
