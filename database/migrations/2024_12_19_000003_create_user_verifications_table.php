<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'individual' or 'company'
            $table->unsignedBigInteger('user_id');
            $table->boolean('email_verified')->default(false);
            $table->string('email_token')->nullable();
            $table->boolean('whatsapp_verified')->default(false);
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_code')->nullable();
            $table->boolean('linkedin_verified')->default(false);
            $table->string('linkedin_id')->nullable();
            $table->boolean('profile_completed')->default(false);
            $table->enum('status', ['pending', 'verified', 'needs_edit', 'awaiting_approval', 'incomplete'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index(['user_type', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_verifications');
    }
}; 
