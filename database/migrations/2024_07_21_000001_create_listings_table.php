<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('listing_type')->default('single-date');
            $table->date('starting_date')->nullable();
            $table->date('finishing_date')->nullable();
            $table->string('listing_title')->nullable();
            $table->text('listing_description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('min_capacity')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('experience_level')->nullable();
            $table->string('fitness_level')->nullable();
            $table->text('activities_included')->nullable();
            $table->string('group_language')->nullable();
            $table->json('maps_and_routes')->nullable();
            $table->string('locations')->nullable();
            $table->json('listing_media')->nullable();
            $table->json('promotional_video')->nullable();
            $table->text('whats_included')->nullable();
            $table->text('whats_not_included')->nullable();
            $table->text('additional_notes')->nullable();
            $table->text('providers_faq')->nullable();
            $table->string('personal_policies')->nullable();
            $table->text('personal_policies_text')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
}; 
