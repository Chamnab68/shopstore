<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referee_id')->nullable()->constrained('users')->nullOnDelete(); // null until registered
            $table->string('referral_link_code', 30)->unique();
            $table->enum('status', ['pending', 'registered', 'converted', 'rejected_fraud'])->default('pending');
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('converted_at')->nullable();   // first purchase completed
            $table->string('ip_address', 45)->nullable();    // for fraud detection (self-referral)
            $table->boolean('is_flagged_fraud')->default(false);
            $table->string('fraud_reason', 255)->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
