<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auto_save_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('listing_type')->default('single-date'); // single-date, multi-date, open-date
            $table->string('status')->default('on_process');
            $table->json('form_data'); // All form data as JSON
            $table->json('packages')->nullable();
            $table->json('special_addons')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('periods')->nullable(); // For multi-date and open-date
            $table->string('adventure_title')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('min_capacity')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->string('group_language')->nullable();
            $table->string('experience_level')->nullable();
            $table->string('fitness_level')->nullable();
            $table->json('activities_included')->nullable();
            $table->json('maps_and_routes')->nullable();
            $table->json('listing_media')->nullable();
            $table->json('promotional_video')->nullable();
            $table->text('whats_included')->nullable();
            $table->text('whats_not_included')->nullable();
            $table->text('additional_notes')->nullable();
            $table->text('providers_faq')->nullable();
            $table->text('personal_policies')->nullable();
            $table->string('starting_date')->nullable();
            $table->string('finishing_date')->nullable();
            $table->string('min_advance_notice')->nullable();
            $table->string('max_advance_booking')->nullable();
            $table->json('available_days')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_save_listings');
    }
};
