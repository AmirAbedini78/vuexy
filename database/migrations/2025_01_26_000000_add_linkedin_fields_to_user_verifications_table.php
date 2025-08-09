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
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->string('linkedin_email')->nullable()->after('linkedin_id');
            $table->string('linkedin_name')->nullable()->after('linkedin_email');
            $table->string('linkedin_avatar')->nullable()->after('linkedin_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->dropColumn(['linkedin_email', 'linkedin_name', 'linkedin_avatar']);
        });
    }
}; 
