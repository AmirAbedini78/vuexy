<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check if user_id columns exist and add them if they don't
        if (!Schema::hasColumn('individual_users', 'user_id')) {
            Schema::table('individual_users', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('company_users', 'user_id')) {
            Schema::table('company_users', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        // Delete any records that don't have valid user_id references
        DB::table('individual_users')
            ->whereNull('user_id')
            ->orWhereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'individual_users.user_id');
            })
            ->delete();

        DB::table('company_users')
            ->whereNull('user_id')
            ->orWhereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'company_users.user_id');
            })
            ->delete();

        // Now add the foreign key constraints
        Schema::table('individual_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Update existing records to have 'unsure' as default for want_to_be_listed
        DB::table('individual_users')
            ->whereNull('want_to_be_listed')
            ->orWhere('want_to_be_listed', '')
            ->update(['want_to_be_listed' => 'unsure']);

        DB::table('company_users')
            ->whereNull('want_to_be_listed')
            ->orWhere('want_to_be_listed', '')
            ->update(['want_to_be_listed' => 'unsure']);

        // Update the column to have a default value
        Schema::table('individual_users', function (Blueprint $table) {
            $table->string('want_to_be_listed')->default('unsure')->change();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->string('want_to_be_listed')->default('unsure')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individual_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('individual_users', function (Blueprint $table) {
            $table->string('want_to_be_listed')->default(null)->change();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->string('want_to_be_listed')->default(null)->change();
        });
    }
};
