<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('itinerary_accommodations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->string('accommodation')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_accommodations');
    }
};
