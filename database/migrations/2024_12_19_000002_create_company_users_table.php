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
            $table->string('company_name');
            $table->string('business_type');
            $table->string('industry');
            $table->string('registration_number');
            $table->string('tax_id');
            $table->string('website')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('country');
            $table->string('city');
            $table->text('address');
            $table->string('postal_code');
            $table->string('logo')->nullable();
            $table->string('business_license')->nullable();
            $table->string('professional_title');
            $table->text('bio');
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_users');
    }
}; 
